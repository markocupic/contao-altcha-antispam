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

namespace Markocupic\ContaoAltchaAntispam\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Form;
use Markocupic\ContaoAltchaAntispam\Storage\MpFormsManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Terminal42\MultipageFormsBundle\FormManagerFactory;

#[AsHook('processFormData')]
class ProcessFormDataListener
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly FormManagerFactory|null $formManagerFactory,
    ) {
    }

    /**
     * Purge session storage.
     */
    public function __invoke(array $submittedData, array $formData, array|null $files, array $labels, Form $form): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        if (null === $this->formManagerFactory) {
            return;
        }

        if (!$session->isStarted()) {
            return;
        }

        $formManager = $this->formManagerFactory->forFormId($form->id);

        // Get session reference from request
        $sessRefParam = $formManager->getGetParamForSessionReference();
        $sessRef = $request->get($sessRefParam, '');

        if (!$sessRef) {
            return;
        }

        $data = $session->get(MpFormsManager::SESSION_STORAGE_KEY, []);

        unset($data[$sessRef]);

        $session->set(MpFormsManager::SESSION_STORAGE_KEY, $data);
    }
}
