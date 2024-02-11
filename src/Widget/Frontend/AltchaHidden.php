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

namespace Markocupic\ContaoAltchaAntispam\Widget\Frontend;

use Contao\Input;
use Contao\System;
use Contao\Widget;
use Markocupic\ContaoAltchaAntispam\Validator\AltchaValidator;

class AltchaHidden extends Widget
{
    protected $blnSubmitInput = true;
    protected $blnForAttribute = true;
    protected $strTemplate = 'form_altcha_hidden';
    protected $strPrefix = 'widget widget-altcha-hidden';

    public function generate(): string
    {
        // Not actually used
        return '';
    }

    /**
     * @param mixed $varInput
     *
     * @return mixed
     */
    protected function validator($varInput)
    {
        $payload = Input::postRaw('altcha', '');

        /** @var AltchaValidator $altcha */
        $altcha = System::getContainer()->get(AltchaValidator::class);

        if (!$altcha->validator($payload)) {
            $this->addError($GLOBALS['TL_LANG']['ERR']['altcha_verification_failed']);
        }

        return $varInput;
    }
}
