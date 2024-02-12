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

namespace Markocupic\MathbuchLearningObjectives\EventListener;

use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\CoreBundle\Event\MenuEvent;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Markocupic\MathbuchLearningObjectives\Controller\MathbuchObjectiveDocxExportController;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(ContaoCoreEvents::BACKEND_MENU_BUILD, priority: -255)]
readonly class BackendMenuListener
{
    public function __construct(
        private RequestStack $requestStack,
        private RouterInterface $router,
        private Security $security,
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(MenuEvent $event): void
    {
        if (!$this->checkPermission()) {
            return;
        }

        $factory = $event->getFactory();
        $tree = $event->getTree();

        if ('mainMenu' !== $tree->getName()) {
            return;
        }

        // Add an entry to the Contao backend menu
        $contentNode = $tree->getChild(MathbuchObjectiveDocxExportController::BACKEND_MODULE_CATEGORY);

        $node = $factory
            ->createItem(MathbuchObjectiveDocxExportController::BACKEND_MODULE_TYPE)
            ->setUri($this->router->generate(MathbuchObjectiveDocxExportController::class))
            ->setLabel($this->translator->trans('MOD.'.MathbuchObjectiveDocxExportController::BACKEND_MODULE_TYPE.'.0', [], 'contao_default'))
            ->setLinkAttribute('title', $this->translator->trans('MOD.'.MathbuchObjectiveDocxExportController::BACKEND_MODULE_TYPE.'.1', [], 'contao_default'))
            ->setLinkAttribute('class', MathbuchObjectiveDocxExportController::BACKEND_MODULE_TYPE)
            ->setCurrent(MathbuchObjectiveDocxExportController::BACKEND_MODULE_TYPE === $this->requestStack->getCurrentRequest()->get('_controller'))
        ;

        $contentNode->addChild($node);
    }

    private function checkPermission(): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($this->security->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_MODULE, MathbuchObjectiveDocxExportController::BACKEND_MODULE_TYPE)) {
            return true;
        }

        return false;
    }
}
