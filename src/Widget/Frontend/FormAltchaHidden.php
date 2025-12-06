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

namespace Markocupic\ContaoAltchaAntispam\Widget\Frontend;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\StringUtil;
use Contao\System;
use Contao\Widget;
use Markocupic\ContaoAltchaAntispam\Altcha\AltchaValidator;
use Markocupic\ContaoAltchaAntispam\Altcha\AltchaWidgetAttributes;
use Markocupic\ContaoAltchaAntispam\Controller\AltchaController;
use Markocupic\ContaoAltchaAntispam\Storage\MpFormsManager;

class FormAltchaHidden extends Widget
{
    protected $useRawRequestData = true;

    protected $blnSubmitInput = false;

    protected $blnForAttribute = true;

    protected $strTemplate = 'form_altcha_hidden';

    protected $prefix = 'widget widget-altcha';

    protected string $strAltchaAttributes = '';

    protected AltchaWidgetAttributes|null $altchaAttributes;

    protected string $altchaAuto = '';

    protected bool $altchaHideLogo;

    protected bool $altchaHideFooter;

    protected int $altchaMaxNumber = 10000000;

    protected int $altchaDelay = 500;

    protected string $altchaSource = 'local';

    /**
     * Add specific attributes.
     *
     * @param string $strKey   The attribute key
     * @param mixed  $varValue The attribute value
     */
    public function __set($strKey, $varValue): void
    {
        switch ($strKey) {
            case 'minlength':
            case 'maxlength':
            case 'minval':
            case 'maxval':
                // Ignore
                break;

            default:
                parent::__set($strKey, $varValue);
                break;
        }
    }

    /**
     * Return a parameter.
     *
     * @param string $strKey The parameter name
     *
     * @return mixed The parameter value
     */
    public function __get($strKey)
    {
        if ('altchaAttributes' === $strKey) {
            return $this->getAltchaAttributes();
        }

        return parent::__get($strKey);
    }

    /**
     * Generate the widget and return it as a string.
     *
     * @return string The widget markup
     */
    public function generate(): string
    {
        return \sprintf(
            '<altcha-widget %s></altcha-widget>',
            $this->getAltchaAttributes()->toString(),
        );
    }

    /**
     * Parse the template file and return it as string.
     *
     * @param array $arrAttributes An optional attributes array
     *
     * @return string The template markup
     */
    public function parse($arrAttributes = null): string
    {
        $request = $this->getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && $this->getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->title = $this->label;

            return $objTemplate->parse();
        }

        /** @var MpFormsManager $mpFormsManager */
        $mpFormsManager = System::getContainer()->get(MpFormsManager::class);

        // Do not show the ALTCHA widget again if it has already been successfully submitted in a terminal42/contao-mp_forms
        if ($mpFormsManager->isPartOfMpForms((int) $this->id)) {
            if ($mpFormsManager->isAltchaAlreadyVerified((int) $this->id)) {
                return '';
            }
        }

        $this->altchaAttributes = $this->getAltchaAttributes();

        return parent::parse($arrAttributes);
    }

    protected function getAltchaAttributes(): AltchaWidgetAttributes
    {
        $attributes = (new AltchaWidgetAttributes())
            ->add('name', $this->name)
            ->add('challengeurl', $this->getContainer()->get('router')->generate(AltchaController::class))
            ->add('maxnumber', $this->altchaMaxNumber)
            ->add('strings', json_encode($this->getLocalization()))
        ;

        if (!empty($this->altchaAuto) && \in_array($this->altchaAuto, ['onload', 'onsubmit'], true)) {
            $attributes = $attributes->add('auto', StringUtil::specialchars($this->altchaAuto));
        }

        if (!empty($this->altchaDelay)) {
            $attributes = $attributes->add('delay', abs($this->altchaDelay));
        }

        if ($this->altchaHideLogo) {
            $attributes = $attributes->add('hidelogo', true);
        }

        if ($this->altchaHideFooter) {
            $attributes = $attributes->add('hidefooter', true);
        }

        if (System::getContainer()->getParameter('kernel.debug')) {
            $attributes = $attributes->add('debug', true);
        }

        return $attributes;
    }

    protected function validator($varInput): mixed
    {
        /** @var MpFormsManager $mpFormsManager */
        $mpFormsManager = System::getContainer()->get(MpFormsManager::class);

        if ($mpFormsManager->isPartOfMpForms((int) $this->id)) {
            if ($mpFormsManager->isAltchaAlreadyVerified((int) $this->id)) {
                return $varInput;
            }
        }

        $payload = $varInput;

        /** @var AltchaValidator $altcha */
        $altcha = $this->getContainer()->get(AltchaValidator::class);

        if (!$payload || !$altcha->validate($payload)) {
            $this->addError($GLOBALS['TL_LANG']['ERR']['altcha_verification_failed']);
        } else {
            // Do only verify the challenge once if the ALTCHA form field is part of a terminal42/contao-mp_forms form.
            if ($mpFormsManager->isPartOfMpForms((int) $this->id)) {
                $mpFormsManager->markAltchaAsVerified((int) $this->id);
            }
        }

        return $varInput;
    }

    protected function getLocalization(): array
    {
        Controller::loadLanguageFile('default');

        return $GLOBALS['TL_LANG']['ALTCHA'];
    }
}
