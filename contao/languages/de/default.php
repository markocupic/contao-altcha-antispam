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
    'Altcha Antispam (CAPTCHA)',
    'Stellt ein <a href="https://altcha.org/" title="altcha.org">ALTCHA</a> Antispam-Formularfeld zur Verfügung, um zu überprüfen, ob das Formular von einem Menschen abgesendet wird (CAPTCHA).',
];

/*
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['altcha_verification_failed'] = 'ALTCHA-Antispam-Überprüfung fehlgeschlagen. Sind Sie ein Mensch oder ein Roboter?';
$GLOBALS['TL_LANG']['ERR']['altcha_hmac_key_not_found'] = 'Der ALTCHA hmac key ist noch leer und sollte noch konfiguriert werden. Bitte fügen Sie diesen in config/config.yaml hinzu. <a href="https://github.com/markocupic/contao-altcha-antispam?tab=readme-ov-file#configuration-and-usage" target="_blank">Mehr</a>';
