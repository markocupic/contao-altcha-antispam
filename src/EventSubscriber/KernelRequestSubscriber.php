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

namespace Markocupic\MathbuchLearningObjectives\EventSubscriber;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Markocupic\MathbuchLearningObjectives\Controller\MathbuchObjectiveDocxExportController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class KernelRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ScopeMatcher $scopeMatcher,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    public function onKernelRequest(RequestEvent $e): void
    {
        $request = $e->getRequest();

        if (!$this->scopeMatcher->isBackendRequest($request)) {
            return;
        }

        if (MathbuchObjectiveDocxExportController::class !== $request->attributes->get('_controller')) {
            return;
        }

        $GLOBALS['TL_CSS'][] = 'bundles/markocupicmathbuchlearningobjectives/css/styles.css';
    }
}
