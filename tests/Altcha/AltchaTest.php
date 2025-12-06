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
use Markocupic\ContaoAltchaAntispam\Altcha\Challenge;
use PHPUnit\Framework\TestCase;

class AltchaTest extends TestCase
{
    private Algorithm $algorithm;

    private Connection $connectionMock;

    private Altcha $altcha;

    protected function setUp(): void
    {
        $this->algorithm = Algorithm::ALGORITHM_SHA_256; // or any other algorithm
        $this->connectionMock = $this->createMock(Connection::class);
        $this->altcha = new Altcha(
            $this->algorithm,
            $this->connectionMock,
            3600, // altchaChallengeExpiry
            100, // altchaRangeMax
            1, // altchaRangeMin
            'secure-hmac-key', // altchaHmacKey
        );
    }

    public function testCreateChallengeSuccess(): void
    {
        $challenge = $this->altcha->createChallenge();
        $this->assertInstanceOf(Challenge::class, $challenge);
        $this->assertGreaterThanOrEqual(1, $challenge->getNumber());
        $this->assertLessThanOrEqual(100, $challenge->getNumber());
        $this->assertSame('secure-hmac-key', $challenge->getHmacKey());
        $this->assertSame(100, $challenge->getMaxNumber());
        $this->assertNotEmpty($challenge->getSalt());
        $this->assertSame($this->algorithm, $challenge->getAlgorithm());
    }

    public function testCreateChallengeWithCustomSaltAndNumber(): void
    {
        $salt = 'custom-salt';
        $number = 42;
        $challenge = $this->altcha->createChallenge($salt, $number);
        $this->assertInstanceOf(Challenge::class, $challenge);
        $this->assertSame(42, $challenge->getNumber());
        $this->assertSame('custom-salt', $challenge->getSalt());
        $this->assertSame('secure-hmac-key', $challenge->getHmacKey());
        $this->assertSame(100, $challenge->getMaxNumber());
        $this->assertSame($this->algorithm, $challenge->getAlgorithm());
    }

    /**
     * @dataProvider algorithmProvider
     */
    public function testCreateChallengeWithDifferentAlgorithms(Algorithm $algorithm): void
    {
        $altcha = new Altcha(
            $algorithm,
            $this->connectionMock,
            3600,
            100,
            1,
            'secure-hmac-key',
        );

        $challenge = $altcha->createChallenge();
        $this->assertInstanceOf(Challenge::class, $challenge);
        $this->assertSame($algorithm, $challenge->getAlgorithm());
    }

    public static function algorithmProvider(): iterable
    {
        return [
            'SHA256' => [Algorithm::ALGORITHM_SHA_256],
            'SHA384' => [Algorithm::ALGORITHM_SHA_384],
            'SHA512' => [Algorithm::ALGORITHM_SHA_512],
        ];
    }
}
