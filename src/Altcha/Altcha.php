<?php

declare(strict_types=1);

/*
 * This file is part of Contao Altcha Antispam.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-altcha-antispam
 */

namespace Markocupic\ContaoAltchaAntispam\Altcha;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Markocupic\ContaoAltchaAntispam\Exception\KeyNotSetException;

class Altcha
{
    public function __construct(
        private readonly Algorithm $altchaAlgorithm,
        private readonly Connection $connection,
        private readonly int $altchaChallengeExpiry,
        private readonly int $altchaRangeMax,
        private readonly int $altchaRangeMin,
        private readonly string $altchaHmacKey,
    ) {
    }

    public function createChallenge(string|null $salt = null, int|null $number = null): Challenge
    {
        if ('' === $this->altchaHmacKey) {
            throw new KeyNotSetException('ALTCHA hmac key ist empty and should be set in config/config.yaml. Please visit https://github.com/markocupic/contao-altcha-antispam?tab=readme-ov-file#configuration-and-usage to learn more.');
        }

        $salt = $salt ?? bin2hex(random_bytes(12));
        $number = $number ?? random_int($this->altchaRangeMin, $this->altchaRangeMax);
        $expiry = time() + $this->altchaChallengeExpiry;

        // Create the challenge
        return new Challenge($salt, $number, $this->altchaHmacKey, $expiry, $this->altchaAlgorithm);
    }

    public function persistChallenge(Challenge $challenge): void
    {
        // The challenge expires in 1 hour (default).
        // We save it to the database to prevent replay attacks.
        $set = [
            'tstamp' => time(),
            'challenge' => $challenge->getChallenge(),
            'expires' => $challenge->getExpiry(),
        ];

        $this->connection->insert('tl_altcha_challenge', $set);
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
            ],
        );

        // Return false if the challenge has expired or has already been solved.
        if (1 !== $rowsAffected) {
            return false;
        }

        // Regenerate the challenge with salt and number from payload.
        $expectedChallenge = $this->createChallenge($json['salt'], $json['number']);

        if (!$this->hasValidAlgorithm($json, $expectedChallenge)) {
            return false;
        }

        if (!$this->hasValidChallenge($json, $expectedChallenge)) {
            return false;
        }

        if (!$this->hasValidSignature($json, $expectedChallenge)) {
            return false;
        }

        return true;
    }

    private function hasValidAlgorithm(array $json, Challenge $challenge): bool
    {
        return $json['algorithm'] === $challenge->getAlgorithm()->value;
    }

    private function hasValidChallenge(array $json, Challenge $challenge): bool
    {
        return $json['challenge'] === $challenge->getChallenge();
    }

    private function hasValidSignature(array $json, Challenge $challenge): bool
    {
        return $json['signature'] === $challenge->getSignature();
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
