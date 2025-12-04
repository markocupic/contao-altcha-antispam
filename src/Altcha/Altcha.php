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
        $this->validateConfiguration();

        $expiry = time() + $this->altchaChallengeExpiry;
        $salt = $salt ?? $this->generateSalt($expiry);
        $number = $number ?? random_int($this->altchaRangeMin, $this->altchaRangeMax);

        // Create the challenge
        return new Challenge($number, $this->altchaRangeMax, $expiry, $this->altchaHmacKey, $salt, $this->altchaAlgorithm);
    }

    public function persistChallenge(Challenge $challenge): void
    {
        // The challenge expires in 1 hour (default).
        // We save it to the database to prevent replay attacks.
        $insertParameters = [
            'tstamp' => time(),
            'challenge' => $challenge->getChallenge(),
            'expires' => $challenge->getExpiry(),
        ];

        $insertTypes = [
            'tstamp' => Types::INTEGER,
            'challenge' => Types::STRING,
            'expires' => Types::INTEGER,
        ];

        $this->connection->insert('tl_altcha_challenge', $insertParameters, $insertTypes);
    }

    private function validateConfiguration(): void
    {
        if ('' === $this->altchaHmacKey) {
            throw new KeyNotSetException('ALTCHA hmac key ist empty and should be set in config/config.yaml. Please visit https://github.com/markocupic/contao-altcha-antispam?tab=readme-ov-file#configuration-and-usage to learn more.');
        }
    }

    private function generateSalt(int $expiry): string
    {
        // Append expiry to salt, which will be part of the signature and verifiable on the server.
        return \sprintf('%s?expires=%s', bin2hex(random_bytes(12)), urlencode((string) $expiry));
    }
}
