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

class AltchaValidator
{
    public function __construct(
        private readonly Altcha $altcha,
        private readonly Connection $connection,
    ) {
    }

    public function validate(string $payload): bool
    {
        $decodedPayload = json_decode(base64_decode($payload, true), true);

        if (null === $decodedPayload) {
            return false;
        }

        if ($this->isReplay($decodedPayload)) {
            return false;
        }

        $updateParameters = [
            'solved' => '1',
            'solution' => $decodedPayload['challenge'],
            'now' => time(),
            'unsolved' => '',
        ];

        $updateTypes = [
            'solved' => Types::STRING,
            'solution' => Types::STRING,
            'now' => Types::INTEGER,
            'unsolved' => Types::STRING,
        ];

        $rowsAffected = $this->connection->executeStatement(
            'UPDATE tl_altcha_challenge SET solved = :solved WHERE challenge = :solution AND expires > :now AND solved = :unsolved',
            $updateParameters,
            $updateTypes,
        );

        // Return false if the challenge has expired or has already been solved.
        if (1 !== $rowsAffected) {
            return false;
        }

        // Regenerate the challenge with salt and number from payload.
        $expectedChallenge = $this->altcha->createChallenge($decodedPayload['salt'], $decodedPayload['number']);

        if (!$this->hasValidAlgorithm($decodedPayload, $expectedChallenge)) {
            return false;
        }

        if (!$this->hasValidChallenge($decodedPayload, $expectedChallenge)) {
            return false;
        }

        if (!$this->hasValidSignature($decodedPayload, $expectedChallenge)) {
            return false;
        }

        return true;
    }

    private function hasValidAlgorithm(array $decodedPayload, Challenge $challenge): bool
    {
        return $decodedPayload['algorithm'] === $challenge->getAlgorithm()->value;
    }

    private function hasValidChallenge(array $decodedPayload, Challenge $challenge): bool
    {
        return $decodedPayload['challenge'] === $challenge->getChallenge();
    }

    private function hasValidSignature(array $decodedPayload, Challenge $challenge): bool
    {
        return $decodedPayload['signature'] === $challenge->getSignature();
    }

    private function isReplay(array $decodedPayload): bool
    {
        $solution = $decodedPayload['challenge'] ?? '';

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
