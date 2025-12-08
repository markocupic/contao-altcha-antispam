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

namespace Markocupic\ContaoAltchaAntispam\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AddContentToHead extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('add_content_to_head', [$this, 'addContentToHead'], ['is_safe' => ['html']]),
        ];
    }

    public function addContentToHead(string $content): void
    {
        if (!isset($GLOBALS['TL_HEAD'])) {
            $GLOBALS['TL_HEAD'] = [];
        }

        $GLOBALS['TL_HEAD'][] = $content;
    }
}
