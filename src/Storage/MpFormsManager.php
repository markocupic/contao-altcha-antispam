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

namespace Markocupic\ContaoAltchaAntispam\Storage;

use Contao\FormFieldModel;
use Contao\FormModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Terminal42\MultipageFormsBundle\FormManager;
use Terminal42\MultipageFormsBundle\FormManagerFactory;

class MpFormsManager
{
    public const SESSION_STORAGE_KEY = 'contao.altcha_antispam.mp_forms';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly FormManagerFactory|null $formManagerFactory = null,
    ) {
    }

    public function isPartOfMpForms(int $formFieldId): bool
    {
        if (null === $this->formManagerFactory) {
            return false;
        }

        return false !== $this->getFormManagerFromFormFieldId($formFieldId);
    }

    public function isAltchaAlreadyVerified(int $formFieldId): bool
    {
        if (null === $this->formManagerFactory) {
            return false;
        }

        $formModel = $this->getFormModelFromFormFieldId($formFieldId);

        if (null === $formModel) {
            return false;
        }

        $mpFormsManager = $this->getFormManagerFromFormFieldId($formFieldId);

        if (empty($mpFormsManager) || empty($mpFormsManager->getSessionReference())) {
            return false;
        }

        $session = $this->requestStack->getCurrentRequest()->getSession();

        $data = $session->get(self::SESSION_STORAGE_KEY, []);

        if (empty($data[$mpFormsManager->getSessionReference()]['form_field_'.$formFieldId])) {
            return false;
        }

        if ('verified' === $data[$mpFormsManager->getSessionReference()]['form_field_'.$formFieldId]) {
            return true;
        }

        return false;
    }

    public function markAltchaAsVerified(int $formFieldId): void
    {
        if (null === $this->formManagerFactory) {
            return;
        }

        $formModel = $this->getFormModelFromFormFieldId($formFieldId);

        if (null === $formModel) {
            return;
        }

        $mpFormsManager = $this->getFormManagerFromFormFieldId($formFieldId);

        if (empty($mpFormsManager) || empty($mpFormsManager->getSessionReference())) {
            return;
        }

        $session = $this->requestStack->getCurrentRequest()->getSession();

        $data = $session->get(self::SESSION_STORAGE_KEY, []);
        $data[$mpFormsManager->getSessionReference()]['form_field_'.$formFieldId] = 'verified';
        $session->set(self::SESSION_STORAGE_KEY, $data);
    }

    private function getFormManagerFromFormFieldId(int $formFieldId): FormManager|null
    {
        if (null === $this->formManagerFactory) {
            return null;
        }

        $formModel = $this->getFormModelFromFormFieldId($formFieldId);

        if (null === $formModel) {
            return null;
        }

        return $this->formManagerFactory->forFormId($formModel->id);
    }

    private function getFormModelFromFormFieldId(int $formFieldId): FormModel|null
    {
        $ffModel = FormFieldModel::findOneById($formFieldId);

        return $ffModel?->getRelated('pid');
    }
}
