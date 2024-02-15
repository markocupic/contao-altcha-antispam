<?php

declare(strict_types=1);

/*
 * This file is part of Contao Altcha Antispam.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-altcha-antispam
 */

namespace Markocupic\ContaoAltchaAntispam;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Markocupic\ContaoAltchaAntispam\Config\AltchaAlgorithmConfig;
use Markocupic\ContaoAltchaAntispam\Exception\InvalidAlgorithmException;
use Markocupic\ContaoAltchaAntispam\Exception\KeyNotSetException;

class Altcha
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $altchaHmacKey,
        private readonly string $altchaAlgorithm,
        private readonly int $altchaRangeMin,
        private readonly int $altchaRangeMax,
        private readonly int $altchaChallengeExpiry,
    ) {
    }

    /**
     * @throws InvalidAlgorithmException
     */
    public function createChallenge(string $salt = null, int $number = null): array
    {
        if ('' === $this->altchaHmacKey) {
            throw new KeyNotSetException('ALTCHA hmac key ist empty and should be set in config/config.yaml. Please visit https://github.com/markocupic/contao-altcha-antispam?tab=readme-ov-file#configuration-and-usage to learn more.');
        }

        $salt = $salt ?? bin2hex(random_bytes(12));
        $number = $number ?? random_int($this->altchaRangeMin, $this->altchaRangeMax);

        if (!\in_array($this->altchaAlgorithm, AltchaAlgorithmConfig::ALGORITHM_ALL, true)) {
            throw new InvalidAlgorithmException(sprintf('Algorithm must be set to %s.', implode(', ', AltchaAlgorithmConfig::ALGORITHM_ALL)));
        }

        $algorithm = str_replace('-', '', strtolower($this->altchaAlgorithm));

        $challenge = hash($algorithm, $salt.$number);
        $signature = hash_hmac($algorithm, $challenge, $this->altchaHmacKey);

        // The challenge expires in 1 hour (default).
        // We save it to the database to prevent replay attacks.
        $set = [
            'tstamp' => time(),
            'challenge' => $challenge,
            'expires' => time() + $this->altchaChallengeExpiry,
        ];

        $this->connection->insert('tl_altcha_challenge', $set);

        return [
            'algorithm' => $this->altchaAlgorithm,
            'challenge' => $challenge,
            'salt' => $salt,
            'signature' => $signature,
        ];
    }

    public function isValidPayload(string $payload): bool
    {
        $json = json_decode(base64_decode($payload, true), true);

        if (null === $json) {
            return false;
        }

        if ($this->isReplay($json)) {
            return false;
        }

        $rowsAffected = $this->connection->executeStatement(
            'UPDATE tl_altcha_challenge SET solved = :solved WHERE challenge = :solution AND expires > :now AND solved = :unsolved',
            [
                'solved' => '1',
                'solution' => $json['challenge'],
                'now' => time(),
                'unsolved' => '',
            ],
            [
                'solved' => Types::STRING,
                'solution' => Types::STRING,
                'now' => Types::INTEGER,
                'unsolved' => Types::STRING,
            ]
        );

        // Return false, if challenge has expired or has already been solved
        if (1 !== $rowsAffected) {
            return false;
        }

        $check = $this->createChallenge($json['salt'], $json['number']);

        return $json['algorithm'] === $check['algorithm']
            && $json['challenge'] === $check['challenge']
            && $json['signature'] === $check['signature'];
    }

    private function isReplay(array $json): bool
    {
        $solution = $json['challenge'] ?? '';

        return false !== $this->connection->fetchOne(
            'SELECT id FROM tl_altcha_challenge WHERE challenge = :solution AND solved = :solved',
            [
                'solution' => $solution,
                'solved' => '1',
            ],
            [
                'solution' => Types::STRING,
                'solved' => Types::STRING,
            ],
        );
    }
}
