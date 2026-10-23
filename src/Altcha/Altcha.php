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

class Altcha
{
    public function __construct(
        private readonly Algorithm $altchaAlgorithm,
        private readonly int $altchaChallengeExpiry,
        private readonly int $altchaRangeMax,
        private readonly int $altchaRangeMin,
        private readonly string $altchaHmacKey,
    ) {
    }

    /**
     * Challenges are stateless: The expiry is part of the salt and thus part of
     * the signed challenge. Only solved challenges are stored (see AltchaValidator).
     */
    public function createChallenge(string|null $salt = null, int|null $number = null): Challenge
    {
        $expiry = time() + $this->altchaChallengeExpiry;
        $salt ??= $this->generateSalt($expiry);
        $number ??= random_int($this->altchaRangeMin, $this->altchaRangeMax);

        return new Challenge($number, $this->altchaRangeMax, $expiry, $this->altchaHmacKey, $salt, $this->altchaAlgorithm);
    }

    /**
     * Return the expiry timestamp that has been appended to the salt or null if the
     * salt does not contain a valid expiry parameter.
     */
    public function getExpiryFromSalt(string $salt): int|null
    {
        $query = explode('?', $salt, 2)[1] ?? '';

        parse_str($query, $params);

        $expires = $params['expires'] ?? null;

        if (!\is_string($expires) || !ctype_digit($expires)) {
            return null;
        }

        return (int) $expires;
    }

    private function generateSalt(int $expiry): string
    {
        // Append the expiry parameter to salt, which will be part of the signature and verifiable on the server.
        // Be aware: Each parameter must be terminated with the & delimiter to prevent Challenge Splicing and Replay.
        // https://altcha.org/security-advisory/
        return \sprintf('%s?expires=%s&', bin2hex(random_bytes(12)), urlencode((string) $expiry));
    }
}
