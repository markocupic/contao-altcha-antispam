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
use Markocupic\ContaoAltchaAntispam\Altcha\Algorithm;
use Markocupic\ContaoAltchaAntispam\Altcha\Altcha;
use Markocupic\ContaoAltchaAntispam\Altcha\AltchaValidator;
use Markocupic\ContaoAltchaAntispam\Altcha\Challenge;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AltchaValidatorTest extends TestCase
{
    private AltchaValidator $validator;

    private Altcha|MockObject $altchaMock;

    private Connection|MockObject $connectionMock;

    protected function setUp(): void
    {
        $this->altchaMock = $this->createMock(Altcha::class);
        $this->connectionMock = $this->createMock(Connection::class);
        $this->validator = new AltchaValidator($this->altchaMock, $this->connectionMock);
    }

    public function testValidateReturnsFalseForInvalidPayload(): void
    {
        $this->connectionMock
            ->expects($this->never())
            ->method('executeStatement')
        ;

        $this->assertFalse($this->validator->validate('invalidPayload'));
    }

    public function testValidateReturnsFalseForReplay(): void
    {
        $decodedPayload = ['challenge' => 'testChallenge'];
        $payload = base64_encode(json_encode($decodedPayload));

        $this->connectionMock
            ->method('fetchOne')
            ->with(
                'SELECT id FROM tl_altcha_challenge WHERE challenge = :solution AND solved = :solved',
                $this->callback(static fn ($params) => 'testChallenge' === $params['solution'] && '1' === $params['solved']),
            )
            ->willReturn(1)
        ;

        $this->assertFalse($this->validator->validate($payload));
    }

    public function testIsReplayReturnsTrueForReplayChallenge(): void
    {
        $decodedPayload = ['challenge' => 'replayChallenge'];

        $this->connectionMock
            ->method('fetchOne')
            ->with(
                'SELECT id FROM tl_altcha_challenge WHERE challenge = :solution AND solved = :solved',
                $this->callback(static fn ($params) => 'replayChallenge' === $params['solution'] && '1' === $params['solved']),
                $this->anything(),
            )
            ->willReturn(1)
        ;

        $result = $this->invokeMethod($this->validator, 'isReplay', [$decodedPayload]);

        $this->assertTrue($result);
    }

    public function testIsReplayReturnsFalseForNonReplayChallenge(): void
    {
        $decodedPayload = ['challenge' => 'uniqueChallenge'];

        $this->connectionMock
            ->method('fetchOne')
            ->with(
                'SELECT id FROM tl_altcha_challenge WHERE challenge = :solution AND solved = :solved',
                $this->callback(static fn ($params) => 'uniqueChallenge' === $params['solution'] && '1' === $params['solved']),
                $this->anything(),
            )
            ->willReturn(false)
        ;

        $result = $this->invokeMethod($this->validator, 'isReplay', [$decodedPayload]);

        $this->assertFalse($result);
    }

    public function testValidateReturnsFalseForExpiredChallenge(): void
    {
        $decodedPayload = ['challenge' => 'testChallenge', 'salt' => 'testSalt', 'number' => 123];
        $payload = base64_encode(json_encode($decodedPayload));

        $this->connectionMock
            ->method('fetchOne')
            ->willReturn(false)
        ;

        $this->connectionMock
            ->method('executeStatement')
            ->willReturn(0)
        ;

        $this->assertFalse($this->validator->validate($payload));
    }

    public function testValidateReturnsFalseForInvalidAlgorithm(): void
    {
        $decodedPayload = ['challenge' => 'testChallenge', 'salt' => 'testSalt', 'number' => 123, 'algorithm' => 'wrongAlgorithm'];
        $payload = base64_encode(json_encode($decodedPayload));

        $challengeMock = $this->createMock(Challenge::class);
        $challengeMock
            ->method('getAlgorithm')
            ->willReturn(Algorithm::ALGORITHM_SHA_256)
        ;

        $this->altchaMock
            ->method('createChallenge')
            ->willReturn($challengeMock)
        ;

        $this->connectionMock
            ->method('fetchOne')
            ->willReturn(false)
        ;

        $this->connectionMock
            ->method('executeStatement')
            ->willReturn(1)
        ;

        $this->assertFalse($this->validator->validate($payload));
    }

    public function testValidateReturnsFalseForInvalidChallenge(): void
    {
        $decodedPayload = ['challenge' => 'wrongChallenge', 'salt' => 'testSalt', 'number' => 123, 'algorithm' => 'correctAlgorithm'];
        $payload = base64_encode(json_encode($decodedPayload));

        $challengeMock = $this->createMock(Challenge::class);
        $challengeMock
            ->method('getAlgorithm')
            ->willReturn(Algorithm::ALGORITHM_SHA_256)
        ;

        $challengeMock
            ->method('getChallenge')
            ->willReturn('correctChallenge')
        ;

        $this->altchaMock
            ->method('createChallenge')
            ->willReturn($challengeMock)
        ;

        $this->connectionMock
            ->method('fetchOne')
            ->willReturn(false)
        ;

        $this->connectionMock
            ->method('executeStatement')
            ->willReturn(1)
        ;

        $this->assertFalse($this->validator->validate($payload));
    }

    public function testValidateReturnsFalseForInvalidSignature(): void
    {
        $decodedPayload = [
            'challenge' => 'correctChallenge',
            'salt' => 'testSalt',
            'number' => 123,
            'algorithm' => 'correctAlgorithm',
            'signature' => 'wrongSignature',
        ];
        $payload = base64_encode(json_encode($decodedPayload));

        $challengeMock = $this->createMock(Challenge::class);
        $challengeMock
            ->method('getAlgorithm')
            ->willReturn(Algorithm::ALGORITHM_SHA_256)
        ;

        $challengeMock
            ->method('getChallenge')
            ->willReturn('correctChallenge')
        ;

        $challengeMock
            ->method('getSignature')
            ->willReturn('correctSignature')
        ;

        $this->altchaMock
            ->method('createChallenge')
            ->willReturn($challengeMock)
        ;

        $this->connectionMock
            ->method('fetchOne')
            ->willReturn(false)
        ;

        $this->connectionMock
            ->method('executeStatement')
            ->willReturn(1)
        ;

        $this->assertFalse($this->validator->validate($payload));
    }

    public function testValidateReturnsTrueForValidPayload(): void
    {
        $decodedPayload = [
            'challenge' => 'correctChallenge',
            'salt' => 'testSalt',
            'number' => 123,
            'algorithm' => Algorithm::ALGORITHM_SHA_256->value,
            'signature' => 'correctSignature',
        ];

        $payload = base64_encode(json_encode($decodedPayload));

        $challengeMock = $this->createMock(Challenge::class);
        $challengeMock
            ->method('getAlgorithm')
            ->willReturn(Algorithm::ALGORITHM_SHA_256)
        ;

        $challengeMock
            ->method('getChallenge')
            ->willReturn('correctChallenge')
        ;

        $challengeMock
            ->method('getSignature')
            ->willReturn('correctSignature')
        ;

        $this->altchaMock
            ->method('createChallenge')
            ->willReturn($challengeMock)
        ;

        $this->connectionMock
            ->method('fetchOne')
            ->willReturn(false)
        ;

        $this->connectionMock
            ->method('executeStatement')
            ->willReturn(1)
        ;

        $this->assertTrue($this->validator->validate($payload));
    }

    /**
     * Helper method to execute private/protected methods.
     *
     * @throws \ReflectionException
     */
    private function invokeMethod(object $object, string $methodName, array $arguments = [])
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $arguments);
    }
}
