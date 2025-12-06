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
 * Form fields
 */
$GLOBALS['TL_LANG']['FFL']['altcha_hidden'] = [
    'Altcha Antispam (CAPTCHA)',
    'Stellt ein <a href="https://altcha.org/" target="_blank" aria-label="Besuche Altcha.org">ALTCHA</a> Antispam-Formularfeld zur Verfügung, um zu überprüfen, ob das Formular von einem Menschen abgesendet wird (CAPTCHA).',
];

/*
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['altcha_max_iteration_range'] = 'Damit das Widget fehlerfrei läuft, müssen Sie einen Wert grösser als %d eingeben! Siehe Bundle Konfiguration <code>markocupic_contao_altcha_antispam.range_max</code>.';
$GLOBALS['TL_LANG']['ERR']['altcha_verification_failed'] = 'ALTCHA-Antispam-Überprüfung fehlgeschlagen. Sind Sie ein Mensch oder ein Roboter?';

// Widget translations: see https://github.com/altcha-org/altcha/blob/main/dist_i18n/en.js
$GLOBALS['TL_LANG']['ALTCHA']['ariaLinkLabel'] = 'Besuche Altcha.org';
$GLOBALS['TL_LANG']['ALTCHA']['enterCode'] = 'Code eingeben';
$GLOBALS['TL_LANG']['ALTCHA']['enterCodeAria'] = 'Geben Sie den Code ein, den Sie hören. Drücken Sie die Leertaste, um die Audio abzuspielen.';
$GLOBALS['TL_LANG']['ALTCHA']['error'] = 'Überprüfung fehlgeschlagen. Bitte versuchen Sie es später erneut.';
$GLOBALS['TL_LANG']['ALTCHA']['expired'] = 'Überprüfung abgelaufen. Bitte versuchen Sie es erneut.';
$GLOBALS['TL_LANG']['ALTCHA']['verificationRequired'] = 'Überprüfung erforderlich!';
$GLOBALS['TL_LANG']['ALTCHA']['footer'] = 'Geschützt durch <a href="https://altcha.org/" target="_blank" aria-label="Besuche Altcha.org">ALTCHA</a>';
$GLOBALS['TL_LANG']['ALTCHA']['getAudioChallenge'] = 'Audio-Herausforderung anfordern';
$GLOBALS['TL_LANG']['ALTCHA']['label'] = 'Ich bin kein Roboter';
$GLOBALS['TL_LANG']['ALTCHA']['loading'] = 'Lade...';
$GLOBALS['TL_LANG']['ALTCHA']['reload'] = 'Neu laden';
$GLOBALS['TL_LANG']['ALTCHA']['verify'] = 'Überprüfen';
$GLOBALS['TL_LANG']['ALTCHA']['verified'] = 'Überprüft';
$GLOBALS['TL_LANG']['ALTCHA']['verifying'] = 'Wird überprüft...';
$GLOBALS['TL_LANG']['ALTCHA']['waitAlert'] = 'Überprüfung läuft... bitte warten.';
