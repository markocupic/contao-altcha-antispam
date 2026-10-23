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

/**
 * @deprecated Deprecated since version 0.8, to be removed in version 1.0;
 *             use the {% add to head %} tag of Contao instead.
 */
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
        trigger_deprecation('markocupic/contao-altcha-antispam', '0.8', 'Using the "add_content_to_head()" Twig function is deprecated and will no longer work in version 1.0. Use the "{% add to head %}" tag instead.');

        if (!isset($GLOBALS['TL_HEAD'])) {
            $GLOBALS['TL_HEAD'] = [];
        }

        $GLOBALS['TL_HEAD'][] = $content;
    }
}
