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

namespace Markocupic\ContaoAltchaAntispam\Controller;

use Markocupic\ContaoAltchaAntispam\Altcha\Altcha;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/_contao_altcha_challenge', name: self::class)]
class AltchaController
{
    public function __construct(
        private readonly Altcha $altcha,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $challenge = $this->altcha->createChallenge();
        $this->altcha->persistChallenge($challenge);

        return new JsonResponse($challenge->toArray());
    }
}
