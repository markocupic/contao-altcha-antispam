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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Types\Types;

class AltchaValidator
{
    /**
     * A regular ALTCHA payload is about 300 bytes long. Larger payloads are
     * rejected before decoding them.
     */
    private const MAX_PAYLOAD_LENGTH = 4096;

    public function __construct(
        private readonly Altcha $altcha,
        private readonly Connection $connection,
    ) {
    }

    public function validate(string $payload): bool
    {
        $data = $this->decodePayload($payload);

        if (null === $data) {
            return false;
        }

        // The expiry is part of the salt and therefore protected by the signature.
        $expiry = $this->altcha->getExpiryFromSalt($data['salt']);

        if (null === $expiry || $expiry < time()) {
            return false;
        }

        // Regenerate the challenge with salt and number from payload.
        $expectedChallenge = $this->altcha->createChallenge($data['salt'], $data['number']);

        if (!hash_equals($expectedChallenge->getAlgorithm()->value, $data['algorithm'])) {
            return false;
        }

        if (!hash_equals((string) $expectedChallenge->getChallenge(), $data['challenge'])) {
            return false;
        }

        if (!hash_equals((string) $expectedChallenge->getSignature(), $data['signature'])) {
            return false;
        }

        // Store the solved challenge to prevent replay attacks. The unique index
        // on the challenge column makes this check atomic.
        return $this->markAsSolved($data['challenge'], $expiry);
    }

    /**
     * @return array{algorithm: string, challenge: string, number: int, salt: string, signature: string}|null
     */
    private function decodePayload(string $payload): array|null
    {
        if ('' === $payload || \strlen($payload) > self::MAX_PAYLOAD_LENGTH) {
            return null;
        }

        $json = base64_decode($payload, true);

        if (false === $json) {
            return null;
        }

        try {
            $data = json_decode($json, true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        if (!\is_array($data)) {
            return null;
        }

        foreach (['algorithm', 'challenge', 'salt', 'signature'] as $key) {
            if (!isset($data[$key]) || !\is_string($data[$key]) || '' === $data[$key]) {
                return null;
            }
        }

        if (!isset($data['number']) || !\is_int($data['number']) || $data['number'] < 0) {
            return null;
        }

        return $data;
    }

    private function markAsSolved(string $challenge, int $expiry): bool
    {
        try {
            $this->connection->insert(
                'tl_altcha_challenge',
                [
                    'tstamp' => time(),
                    'challenge' => $challenge,
                    'solved' => '1',
                    'expires' => $expiry,
                ],
                [
                    'tstamp' => Types::INTEGER,
                    'challenge' => Types::STRING,
                    'solved' => Types::STRING,
                    'expires' => Types::INTEGER,
                ],
            );
        } catch (UniqueConstraintViolationException) {
            // The challenge has already been solved (replay attack).
            return false;
        }

        return true;
    }
}
