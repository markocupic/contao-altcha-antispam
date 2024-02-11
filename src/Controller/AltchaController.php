<?php

declare(strict_types=1);

/*
 * This file is part of Contao Altcha Antispam.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-altcha-antispam
 */

namespace Markocupic\ContaoAltchaAntispam\Controller;

use Markocupic\ContaoAltchaAntispam\Altcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/_contao_altcha_challenge', name: self::class)]
class AltchaController extends AbstractController
{
    public function __construct(
        private readonly Altcha $altcha,
    ) {
    }

    /**
     * @return JsonResponse
     * @throws \Markocupic\ContaoAltchaAntispam\Exception\InvalidAlgorithmException
     */
    public function __invoke(): JsonResponse
    {
        return $this->json($this->altcha->createChallenge());
    }
}
