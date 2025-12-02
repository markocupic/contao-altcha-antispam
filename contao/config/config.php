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

use Markocupic\ContaoAltchaAntispam\Widget\Frontend\FormAltchaHidden;

$GLOBALS['TL_FFL']['altcha_hidden'] = FormAltchaHidden::class;
