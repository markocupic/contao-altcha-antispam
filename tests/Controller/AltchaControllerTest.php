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

namespace Markocupic\ContaoAltchaAntispam\Tests\Controller;

use Markocupic\ContaoAltchaAntispam\Altcha\Altcha;
use Markocupic\ContaoAltchaAntispam\Altcha\Challenge;
use Markocupic\ContaoAltchaAntispam\Controller\AltchaController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AltchaControllerTest.
 *
 * Tests the `__invoke` method of the `AltchaController` class.
 *
 * This test ensures that the method:
 * - Generates a challenge using the Altcha service
 * - Persists the generated challenge
 * - Returns a JSON response containing the challenge data
 */
class AltchaControllerTest extends TestCase
{
    public function testInvokeReturnsJsonResponse(): void
    {
        // Create a mock for the Altcha service
        $altchaMock = $this->createMock(Altcha::class);

        // Create a mock challenge
        $challengeMock = $this->createMock(Challenge::class);

        // Return a mock challenge when `createChallenge` is called
        $altchaMock
            ->expects($this->once())
            ->method('createChallenge')
            ->willReturn($challengeMock)
        ;

        // Ensure `persistChallenge` is called with the mock challenge
        $altchaMock
            ->expects($this->once())
            ->method('persistChallenge')
            ->with($challengeMock)
        ;

        // Mock the `toArray` method of the Challenge to return sample challenge data
        $challengeMock
            ->expects($this->once())
            ->method('toArray')
            ->willReturn(['key' => 'value'])
        ;

        // Instantiate the controller with the mocked Altcha service
        $controller = new AltchaController($altchaMock);

        // Call the `__invoke` method
        $response = $controller();

        // Assert the response is a JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Assert the response contains the sample challenge data
        $expectedResponseData = ['key' => 'value'];
        $this->assertSame($expectedResponseData, json_decode($response->getContent(), true));
    }
}
