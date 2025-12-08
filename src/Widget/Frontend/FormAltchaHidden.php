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
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class FormAltchaHidden extends Widget
{
    protected $useRawRequestData = true;

    protected $blnSubmitInput = false;

    protected $blnForAttribute = true;

    protected $strTemplate = 'form_altcha_hidden';

    protected $prefix = 'widget widget-altcha';

    protected AltchaWidgetAttributes|null $altchaAttributes;

    protected string $honeypot = '';

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
        return parent::__get($strKey);
    }

    /**
     * Generate the widget and return it as a string.
     *
     * @return string The widget markup
     */
    public function generate(): string
    {
        $this->setAltchaAttributes();

        return \sprintf(
            '<altcha-widget %s></altcha-widget>',
            $this->altchaAttributes->toString(false),
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

        $this->setAltchaAttributes();
        $this->renderHoneypot();

        return parent::parse($arrAttributes);
    }

    public function generateHoneypotInputName(): string
    {
        $pool = System::getContainer()->getParameter('markocupic_contao_altcha_antispam.honeypot_fieldname_pool');

        if (empty($pool)) {
            $base = 'hp';
        } else {
            $base = $pool[array_rand($pool)];
        }

        $suffix = bin2hex(random_bytes(3)); // random 6‑char suffix

        return $base.'_'.$suffix;
    }

    public function generateHoneypotVerifyInputName(): string
    {
        return hash('sha256', $this->name.$this->id.System::getContainer()->getParameter('kernel.secret'));
    }

    protected function validator($varInput): mixed
    {
        /** @var Request $request */
        $request = $this->getContainer()->get('request_stack')->getCurrentRequest();

        /** @var LoggerInterface $logger */
        $logger = System::getContainer()->get('monolog.logger.contao.error');

        if (!$this->validateHoneypot($request)) {
            $this->addError($GLOBALS['TL_LANG']['ERR']['altcha_verification_failed']);
            $logger->error('Could not process form '.$this->name.'. Spambot validation failed.');

            return $varInput;
        }

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
            $logger->error('Could not process form '.$this->name.'. Spambot validation failed.');
        } else {
            // Do only verify the challenge once if the ALTCHA form field is part of a terminal42/contao-mp_forms form.
            if ($mpFormsManager->isPartOfMpForms((int) $this->id)) {
                $mpFormsManager->markAltchaAsVerified((int) $this->id);
            }
        }

        return $varInput;
    }

    protected function setAltchaAttributes(): void
    {
        $attributes = (new AltchaWidgetAttributes())
            ->add('name', $this->name)
            ->add('challengeurl', $this->getContainer()->get('router')->generate(AltchaController::class))
            ->add('maxnumber', $this->altchaMaxNumber)
            ->add('data-form-field-id', $this->id)
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

        $this->altchaAttributes = $attributes;
    }

    protected function renderHoneypot(): void
    {
        $honeypotInputName = $this->generateHoneypotInputName();
        $hashedHoneypotInputName = hash('sha256', $honeypotInputName.System::getContainer()->getParameter('kernel.secret'));

        /** @var Environment $twig */
        $twig = System::getContainer()->get('twig');

        $this->honeypot = $twig->render('@MarkocupicContaoAltchaAntispam/honeypot.html.twig', [
            'verify_input_name' => $this->generateHoneypotVerifyInputName(),
            'hashed_honeypot_input_name' => $hashedHoneypotInputName,
            'honeypot_input_name' => $honeypotInputName,
            'class' => str_replace('_', '-', 'widget-'.$honeypotInputName),
            'id' => $this->id,
        ]);
    }

    protected function validateHoneypot(Request $request): bool
    {
        $honeypotVerifyInputName = $this->generateHoneypotVerifyInputName();

        $post = $request->request->all();

        $honeypotVerifyHash = $post[$honeypotVerifyInputName] ?? '';

        if ('' === $honeypotVerifyHash) {
            return false;
        }

        foreach ($post as $key => $value) {
            $expectedHash = hash('sha256', $key.System::getContainer()->getParameter('kernel.secret'));

            // Test if the value is from the honeypot field
            if (!hash_equals($honeypotVerifyHash, $expectedHash)) {
                continue;
            }

            if ('' === $value) {
                return true;
            }

            break;
        }

        return false;
    }

    protected function getLocalization(): array
    {
        Controller::loadLanguageFile('default');

        return $GLOBALS['TL_LANG']['ALTCHA'];
    }
}
