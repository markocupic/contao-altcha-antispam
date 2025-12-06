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
    'ALTCHA antispam (CAPTCHA)',
    'Provides an <a href="https://altcha.org/" target="_blank" aria-label="Visit Altcha.org">ALTCHA</a> antispam form field to verify that the form is being submitted by a human (CAPTCHA).',
];

/*
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['altcha_max_iteration_range'] = 'For the widget to run without errors, you must enter a value greater than %d! See bundle configuration <code>markocupic_contao_altcha_antispam.range_max</code>.';
$GLOBALS['TL_LANG']['ERR']['altcha_verification_failed'] = 'Altcha antispam verification failed. Are you a human or a robot?';

// Widget translations: see https://github.com/altcha-org/altcha/blob/main/dist_i18n/en.js
$GLOBALS['TL_LANG']['ALTCHA']['ariaLinkLabel'] = 'Visit Altcha.org';
$GLOBALS['TL_LANG']['ALTCHA']['enterCode'] = 'Enter code';
$GLOBALS['TL_LANG']['ALTCHA']['enterCodeAria'] = 'Enter code you hear. Press Space to play audio.';
$GLOBALS['TL_LANG']['ALTCHA']['error'] = 'Verification failed. Try again later.';
$GLOBALS['TL_LANG']['ALTCHA']['expired'] = 'Verification expired. Try again.';
$GLOBALS['TL_LANG']['ALTCHA']['verificationRequired'] = 'Verification required!';
$GLOBALS['TL_LANG']['ALTCHA']['footer'] = 'Protected by <a href="https://altcha.org/" target="_blank" aria-label="Visit Altcha.org">ALTCHA</a>';
$GLOBALS['TL_LANG']['ALTCHA']['getAudioChallenge'] = 'Get an audio challenge';
$GLOBALS['TL_LANG']['ALTCHA']['label'] = 'I\'m not a robot';
$GLOBALS['TL_LANG']['ALTCHA']['loading'] = 'Loading...';
$GLOBALS['TL_LANG']['ALTCHA']['reload'] = 'Reload';
$GLOBALS['TL_LANG']['ALTCHA']['verify'] = 'Verify';
$GLOBALS['TL_LANG']['ALTCHA']['verified'] = 'Verified';
$GLOBALS['TL_LANG']['ALTCHA']['verifying'] = 'Verifying...';
$GLOBALS['TL_LANG']['ALTCHA']['waitAlert'] = 'Verifying... please wait.';
