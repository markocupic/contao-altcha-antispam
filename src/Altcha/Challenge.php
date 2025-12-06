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

class Challenge
{
    private string|null $signature;

    private string|null $challenge;

    public function __construct(
        private readonly int $number,
        private readonly int $maxNumber,
        private readonly int $expiry,
        private readonly string $hmacKey,
        private readonly string $salt,
        private readonly Algorithm $algorithm,
    ) {
        $this->create();
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getMaxNumber(): int
    {
        return $this->maxNumber;
    }

    public function getExpiry(): int
    {
        return $this->expiry;
    }

    public function getHmacKey(): string
    {
        return $this->hmacKey;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function getSignature(): string|null
    {
        return $this->signature;
    }

    public function getAlgorithm(): Algorithm
    {
        return $this->algorithm;
    }

    public function getChallenge(): string|null
    {
        return $this->challenge;
    }

    public function toArray(): array
    {
        return [
            'algorithm' => $this->algorithm->value,
            'challenge' => $this->challenge,
            //'maxnumber' => $this->maxNumber, // If we return the max number, the form field setting `tl_form_field.altchaMaxNumber` would have no effect!.
            'salt' => $this->salt,
            'signature' => $this->signature,
        ];
    }

    private function create(): void
    {
        if (null === $this->algorithm) {
            throw new \Exception('Algorithm can not be null.');
        }

        // Convert the algorithm name 'SHA-256' to the hash-function-readable format 'sha256'.
        $algorithm = str_replace('-', '', strtolower($this->algorithm->value));

        $this->challenge = hash($algorithm, \sprintf('%s%d', $this->salt, $this->number));

        $this->signature = hash_hmac($algorithm, $this->challenge, $this->hmacKey);
    }
}
