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
$GLOBALS['TL_LANG']['tl_form_field']['altcha_legend'] = 'ALTCHA Widget Einstellungen';

/*
 * Fields
 */
$GLOBALS['TL_LANG']['tl_form_field']['altchaHideFooter'] = ['Footer ausblenden', 'Blenden Sie Footer (ALTCHA Link) aus.'];
$GLOBALS['TL_LANG']['tl_form_field']['altchaHideLogo'] = ['Logo ausblenden', 'Blenden Sie das ALTCHA Logo aus.'];
$GLOBALS['TL_LANG']['tl_form_field']['altchaAuto'] = ['Überprüfung starten', 'Wählen Sie aus, wie die Überprüfung gestartet werden soll (mögliche Optionen: onload, onsubmit).'];
$GLOBALS['TL_LANG']['tl_form_field']['altchaMaxNumber'] = ['Maximale Iterationen', 'Wählen Sie die maximale Anzahl an Iterationen aus (Standardwert: 1,000,000). Dieser Wert entspricht nicht der Komplexität der Challenge. Die Komplexität muss in der Bundle Konfiguration unter <code>markocupic_contao_altcha_antispam.range_min</code> und <code>markocupic_contao_altcha_antispam.range_max</code> eingestellt werden.'];
$GLOBALS['TL_LANG']['tl_form_field']['altchaSource'] = ['altcha.js Quelle', 'Wählen Sie aus, woher das altcha.js Skript geladen werden soll.'];
$GLOBALS['TL_LANG']['tl_form_field']['altchaDelay'] = ['Verzögerung in ms', 'Da die meisten modernen Geräte die Verifizierung des Proof of Work (PoW) zügig durchführen, empfiehlt es sich, eine künstliche Verzögerung vor der Verifizierung einzustellen. Andernfalls blinkt das Widget möglicherweise nur kurz auf dem Bildschirm des Benutzers auf. Konfigurieren Sie die Verzögerung in Millisekunden.'];
$GLOBALS['TL_LANG']['tl_form_field']['altchaNoErrorLog'] = ['Fehlerprotokollierung deaktivieren', 'Deaktivieren Sie die Protokollierung von Fehlern für dieses ALTCHA Widget. Dies ist nützlich, wenn Sie verhindern möchten, dass wiederholte fehlgeschlagene Versuche von Bots Ihr Fehlerprotokoll überfluten.'];
