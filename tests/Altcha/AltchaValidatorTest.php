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

namespace Markocupic\ContaoAltchaAntispam\Tests\Altcha;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDO\Exception as PDODriverException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Markocupic\ContaoAltchaAntispam\Altcha\Algorithm;
use Markocupic\ContaoAltchaAntispam\Altcha\Altcha;
use Markocupic\ContaoAltchaAntispam\Altcha\AltchaValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AltchaValidatorTest extends TestCase
{
    private Altcha $altcha;

    private Connection&MockObject $connection;

    private AltchaValidator $validator;

    protected function setUp(): void
    {
        $this->altcha = new Altcha(Algorithm::ALGORITHM_SHA_256, 3600, 100, 1, 'secure-hmac-key');
        $this->connection = $this->createMock(Connection::class);
        $this->validator = new AltchaValidator($this->altcha, $this->connection);
    }

    public function testValidateReturnsTrueForValidPayload(): void
    {
        $data = $this->createSolvedPayloadData();

        $this->connection
            ->expects($this->once())
            ->method('insert')
            ->with(
                'tl_altcha_challenge',
                $this->callback(static fn (array $row): bool => $row['challenge'] === $data['challenge'] && '1' === $row['solved'] && $row['expires'] > time()),
            )
            ->willReturn(1)
        ;

        $this->assertTrue($this->validator->validate($this->encode($data)));
    }

    public function testValidateReturnsFalseForReplayedPayload(): void
    {
        $exception = new UniqueConstraintViolationException(PDODriverException::new(new \PDOException('Duplicate entry')), null);

        $this->connection
            ->expects($this->once())
            ->method('insert')
            ->willThrowException($exception)
        ;

        $this->assertFalse($this->validator->validate($this->encode($this->createSolvedPayloadData())));
    }

    /**
     * @dataProvider malformedPayloadProvider
     */
    public function testValidateReturnsFalseForMalformedPayload(string $payload): void
    {
        $this->connection
            ->expects($this->never())
            ->method('insert')
        ;

        $this->assertFalse($this->validator->validate($payload));
    }

    public static function malformedPayloadProvider(): iterable
    {
        $valid = [
            'algorithm' => 'SHA-256',
            'challenge' => 'abc',
            'number' => 42,
            'salt' => 'abc?expires=9999999999&',
            'signature' => 'abc',
        ];

        yield 'empty string' => [''];
        yield 'invalid base64' => ['%%%not-base64%%%'];
        yield 'not json' => [base64_encode('foo')];
        yield 'json scalar' => [base64_encode('42')];
        yield 'json string' => [base64_encode('"foo"')];
        yield 'too long' => [str_repeat('a', 5000)];
        yield 'number as string' => [base64_encode(json_encode(['number' => '42'] + $valid))];
        yield 'negative number' => [base64_encode(json_encode(['number' => -1] + $valid))];
        yield 'challenge as array' => [base64_encode(json_encode(['challenge' => ['abc']] + $valid))];
        yield 'salt as int' => [base64_encode(json_encode(['salt' => 1] + $valid))];

        foreach (array_keys($valid) as $key) {
            $data = $valid;
            unset($data[$key]);

            yield "missing $key" => [base64_encode(json_encode($data))];
        }
    }

    public function testValidateReturnsFalseForExpiredChallenge(): void
    {
        $challenge = $this->altcha->createChallenge('abc?expires='.(time() - 1).'&', 42);

        $data = [
            'algorithm' => $challenge->getAlgorithm()->value,
            'challenge' => $challenge->getChallenge(),
            'number' => $challenge->getNumber(),
            'salt' => $challenge->getSalt(),
            'signature' => $challenge->getSignature(),
        ];

        $this->connection->expects($this->never())->method('insert');

        $this->assertFalse($this->validator->validate($this->encode($data)));
    }

    public function testValidateReturnsFalseForSaltWithoutExpiry(): void
    {
        $challenge = $this->altcha->createChallenge('abc', 42);

        $data = [
            'algorithm' => $challenge->getAlgorithm()->value,
            'challenge' => $challenge->getChallenge(),
            'number' => $challenge->getNumber(),
            'salt' => $challenge->getSalt(),
            'signature' => $challenge->getSignature(),
        ];

        $this->connection->expects($this->never())->method('insert');

        $this->assertFalse($this->validator->validate($this->encode($data)));
    }

    /**
     * @dataProvider tamperedPayloadProvider
     */
    public function testValidateReturnsFalseForTamperedPayload(string $key, mixed $value): void
    {
        $data = $this->createSolvedPayloadData();
        $data[$key] = \is_callable($value) ? $value($data) : $value;

        $this->connection->expects($this->never())->method('insert');

        $this->assertFalse($this->validator->validate($this->encode($data)));
    }

    public static function tamperedPayloadProvider(): iterable
    {
        yield 'wrong number' => ['number', static fn (array $data): int => $data['number'] + 1];
        yield 'wrong algorithm' => ['algorithm', 'SHA-512'];
        yield 'wrong challenge' => ['challenge', str_repeat('a', 64)];
        yield 'wrong signature' => ['signature', str_repeat('a', 64)];
        yield 'extended expiry' => ['salt', static fn (array $data): string => preg_replace('/expires=\d+/', 'expires=9999999999', $data['salt'])];
    }

    /**
     * Simulate the ALTCHA widget: Request a challenge and solve it.
     */
    private function createSolvedPayloadData(): array
    {
        $challenge = $this->altcha->createChallenge();
        $algorithm = str_replace('-', '', strtolower($challenge->getAlgorithm()->value));

        for ($number = 0; $number <= $challenge->getMaxNumber(); ++$number) {
            if (hash($algorithm, $challenge->getSalt().$number) === $challenge->getChallenge()) {
                return [
                    'algorithm' => $challenge->getAlgorithm()->value,
                    'challenge' => $challenge->getChallenge(),
                    'number' => $number,
                    'salt' => $challenge->getSalt(),
                    'signature' => $challenge->getSignature(),
                    'took' => 10,
                ];
            }
        }

        $this->fail('Could not solve the challenge.');
    }

    private function encode(array $data): string
    {
        return base64_encode(json_encode($data, JSON_THROW_ON_ERROR));
    }
}
