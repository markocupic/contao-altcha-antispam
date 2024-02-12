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

/*
 * Form fields
 */
$GLOBALS['TL_LANG']['FFL']['altcha_hidden'] = [
    'ALTCHA antispam (CAPTCHA)',
    'Provides an <a href="https://altcha.org/" title="altcha.org">ALTCHA</a> antispam form field to verify that the form is being submitted by a human (CAPTCHA).',
];

/*
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['altcha_verification_failed'] = 'Altcha antispam verification failed. Are you a human or a robot?';
$GLOBALS['TL_LANG']['ERR']['altcha_hmac_key_not_found'] = 'Set your ALTCHA hmac key in config/config.yaml. <a href="https://github.com/markocupic/contao-altcha-antispam?tab=readme-ov-file#configuration-and-usage" target="_blank">More</a>';
