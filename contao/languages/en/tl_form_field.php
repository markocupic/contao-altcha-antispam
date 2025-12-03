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

/*
 * Legends
 */

$GLOBALS['TL_LANG']['tl_form_field']['altcha_legend'] = 'ALTCHA widget settings';

/*
 * Fields
 */
$GLOBALS['TL_LANG']['tl_form_field']['altchaHideFooter'] = ['hide footer', 'Hide the footer (ALTCHA link).'];
$GLOBALS['TL_LANG']['tl_form_field']['altchaHideLogo'] = ['hide logo', 'Hide the ALTCHA logo.'];
$GLOBALS['TL_LANG']['tl_form_field']['altchaAuto'] = ['verification', 'Automatically start verification without user interaction (possible values: onload, onsubmit).'];
$GLOBALS['TL_LANG']['tl_form_field']['altchaMaxNumber'] = ['max number', 'The max. number to iterate to (defaults to 1,000,000).'];
$GLOBALS['TL_LANG']['tl_form_field']['altchaSource'] = ['altcha.js source', 'Please select where altcha.js should be loaded from.'];
