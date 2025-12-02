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

namespace Markocupic\ContaoAltchaAntispam;

class Challenge
{
    private string|null $signature;

    private string|null $challenge;

    public function __construct(
        private readonly string $salt,
        private readonly int $number,
        private readonly string $hmacKey,
        private readonly int $expiry,
        private readonly Algorithm $algorithm,
    ) {
        $this->create();
    }

    public function getSignature(): string|null
    {
        return $this->signature;
    }

    public function getChallenge(): string|null
    {
        return $this->challenge;
    }

    public function getAlgorithm(): Algorithm
    {
        return $this->algorithm;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function getExpiry(): int
    {
        return $this->expiry;
    }

    public function toArray(): array
    {
        return [
            'algorithm' => $this->algorithm->value,
            'challenge' => $this->challenge,
            'salt' => $this->salt,
            'signature' => $this->signature,
        ];
    }

    private function create(): void
    {
        if (null === $this->algorithm) {
            throw new \Exception('Algorithm can not be null.');
        }

        $algorithm = str_replace('-', '', strtolower($this->algorithm->value));

        $this->challenge = hash($algorithm, $this->salt.$this->number);
        $this->signature = hash_hmac($algorithm, $this->challenge, $this->hmacKey);
    }
}
