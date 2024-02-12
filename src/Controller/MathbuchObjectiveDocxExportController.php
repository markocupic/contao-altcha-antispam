<?php

declare(strict_types=1);

/*
 * This file is part of mathbuch-learning-objectives.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/mathbuch-learning-objectives
 */

namespace Markocupic\MathbuchLearningObjectives\Controller;

use Codefog\HasteBundle\Form\Form;
use Contao\Controller;
use Contao\CoreBundle\Controller\AbstractBackendController;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Message;
use Contao\System;
use Markocupic\MathbuchLearningObjectives\Docx\DocxGenerator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/%contao.backend.route_prefix%/'.self::BACKEND_MODULE_TYPE, name: self::class, defaults: ['_scope' => 'backend'])]
class MathbuchObjectiveDocxExportController extends AbstractBackendController
{
    public const BACKEND_MODULE_TYPE = 'mathbuch_objectives_docx_export';
    public const BACKEND_MODULE_CATEGORY = 'mathbuch';
    private const DOCX_TEMPLATE = 'vendor/markocupic/mathbuch-learning-objectives/docx/mathbuch_template.docx';
    private const TARGET_PATH = 'system/tmp/mathbuch_kompetenzen_band_%s_niveau_%s.docx';

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly Security $security,
        private readonly TranslatorInterface $translator,
        private readonly DocxGenerator $docxGenerator,
        private readonly string $projectDir,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $this->checkPermission();

        $message = $this->framework->getAdapter(Message::class);

        if ($message->hasMessages()) {
            $messages = Message::generateUnwrapped();
        }

        $system = $this->framework->getAdapter(System::class);
        $system->loadLanguageFile('modules');

        $form = $this->getForm();

        if ($form->validate() && 'mathbuch_objectives_docx_export' === $request->request->get('FORM_SUBMIT')) {
            $volume = $request->request->get('volume');
            $level = $request->request->get('level');

            // Get the docx template
            $objTemplate = new \SplFileObject($this->projectDir.'/'.self::DOCX_TEMPLATE);

            // Target path
            $targetPath = sprintf($this->projectDir.'/'.self::TARGET_PATH, $volume, $level);

            $file = $this->docxGenerator->generate($volume, $level, $objTemplate, $targetPath);

            if (null === $file) {
                $message->addInfo($this->translator->trans('MSG.no_objectives_found_for_your_selection', [], 'contao_default'));

                $controller = $this->framework->getAdapter(Controller::class);
                $controller->reload();
            }

            return $this->file($file);
        }

        return $this->render(
            '@MarkocupicMathbuchLearningObjectives/Backend/mathbuch_objectives_docx_export.html.twig',
            [
                'headline' => $this->translator->trans('MOD.'.self::BACKEND_MODULE_TYPE.'.0', [], 'contao_default'),
                'messages' => $messages ?? null,
                'form' => $form->generate(),
            ]
        );
    }

    private function getForm(): Form
    {
        // Get adapters
        $system = $this->framework->getAdapter(System::class);
        $controller = $this->framework->getAdapter(Controller::class);

        // Load languages
        $system->loadLanguageFile('tl_mathbuch_learning_objectives');
        $system->loadLanguageFile('default');

        // Load DCA
        $controller->loadDataContainer('tl_mathbuch_learning_objectives');
        $dca = $GLOBALS['TL_DCA']['tl_mathbuch_learning_objectives'];
        $dcaFields = $dca['fields'];

        // Generate the form object
        $form = new Form(self::BACKEND_MODULE_TYPE, 'POST');

        $form->addFormField('volume', $dcaFields['volume']);
        $form->addFormField('level', [
            'label' => &$GLOBALS['TL_LANG']['MSC']['select_ah_level'],
            'inputType' => 'select',
            'options' => ['ah_basic', 'ah_plus'],
            'reference' => &$GLOBALS['TL_LANG']['MSC']['ah_level'],
            'eval' => ['mandatory' => true],
        ]);

        $form->addSubmitFormField($this->translator->trans('MSC.download_objectives', [], 'contao_default'));

        return $form;
    }

    private function checkPermission(): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        if ($this->security->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_MODULE, self::BACKEND_MODULE_TYPE)) {
            return;
        }

        throw new AccessDeniedException('Access denied');
    }
}
