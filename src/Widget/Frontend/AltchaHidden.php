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

use Contao\BackendTemplate;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Contao\Widget;
use Markocupic\ContaoAltchaAntispam\Controller\AltchaController;
use Markocupic\ContaoAltchaAntispam\Validator\AltchaValidator;
use Symfony\Component\Routing\RouterInterface;

class AltchaHidden extends Widget
{
    protected $blnSubmitInput = false;
    protected $blnForAttribute = true;
    protected $strTemplate = 'form_altcha_hidden';
    protected $strPrefix = 'widget widget-altcha-hidden';
    protected $strAltchaAttributes = '';
    protected $altchaSource = 'local';

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
            return $this->getAltchaAttributesAsString();
        }

        return parent::__get($strKey);
    }

    /**
     * Generate the widget and return it as string.
     *
     * @return string The widget markup
     */
    public function generate()
    {
        return sprintf(
            '<altcha-widget %s></altcha-widget>',
            $this->getAltchaAttributesAsString(),
        );
    }

    /**
     * Parse the template file and return it as string.
     *
     * @param array $arrAttributes An optional attributes array
     *
     * @return string The template markup
     */
    public function parse($arrAttributes = null)
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->title = $this->label;

            return $objTemplate->parse();
        }

        $this->strAltchaAttributes = $this->getAltchaAttributesAsString();

        return parent::parse($arrAttributes);
    }

    protected function getAltchaAttributesAsArray(): array
    {
        /** @var RouterInterface $router */
        $router = System::getContainer()->get('router');

        $endpoint = sprintf('challengeurl="%s"', $router->generate(AltchaController::class, []));

        $attributes = [];
        $attributes[] = $endpoint;

        $attributes[] = 'name="altcha"';

        if (!empty($this->altchaAuto) && \in_array($this->altchaAuto, ['onload', 'onsubmit'], true)) {
            $attributes[] = sprintf('auto="%s"', StringUtil::specialchars($this->altchaAuto));
        }

        if ($this->altchaHideLogo) {
            $attributes[] = 'hidelogo';
        }

        if ($this->altchaHideFooter) {
            $attributes[] = 'hidefooter';
        }

        if (\is_int($this->altchaMaxNumber)) {
            $attributes[] = sprintf('maxnumber="%d"', $this->altchaMaxNumber);
        }

        $localization = StringUtil::specialchars(json_encode($this->getLocalization()));
        $attributes[] = sprintf('strings="%s"', $localization);

        return array_filter(array_unique($attributes));
    }

    protected function getAltchaAttributesAsString(): string
    {
        return implode(' ', $this->getAltchaAttributesAsArray());
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

    protected function getLocalization(): array
    {
        return [
            'error' => $GLOBALS['TL_LANG']['ERR']['altcha_widget_error'],
            'footer' => $GLOBALS['TL_LANG']['ERR']['altcha_widget_footer'],
            'label' => $GLOBALS['TL_LANG']['ERR']['altcha_widget_label'],
            'verified' => $GLOBALS['TL_LANG']['ERR']['altcha_widget_verified'],
            'verifying' => $GLOBALS['TL_LANG']['ERR']['altcha_widget_verifying'],
            'waitAlert' => $GLOBALS['TL_LANG']['ERR']['altcha_widget_waitAlert'],
        ];
    }
}
