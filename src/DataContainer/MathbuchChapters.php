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

namespace Markocupic\MathbuchLearningObjectives\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\SecurityBundle\Security;

readonly class MathbuchChapters
{
    public function __construct(
        private Security $security,
    ) {
    }

    #[AsCallback(table: 'tl_mathbuch_chapters', target: 'config.onload', priority: 100)]
    public function onload(DataContainer $dc): void
    {
        $t = 'tl_mathbuch_chapters';

        if ($this->security->isGranted('ROLE_ADMIN')) {
            unset(
                $GLOBALS['TL_DCA'][$t]['config']['closed'],
                $GLOBALS['TL_DCA'][$t]['config']['notEditable'],
                $GLOBALS['TL_DCA'][$t]['config']['notDeletable'],
                $GLOBALS['TL_DCA'][$t]['config']['notSortable'],
                $GLOBALS['TL_DCA'][$t]['config']['notCopyable'],
                $GLOBALS['TL_DCA'][$t]['config']['notCreatable'],
            );

            return;
        }

        // Do not show operations to default users
        unset(
            $GLOBALS['TL_DCA'][$t]['list']['operations']['edit'],
            $GLOBALS['TL_DCA'][$t]['list']['operations']['copy'],
            $GLOBALS['TL_DCA'][$t]['list']['operations']['delete'],
            $GLOBALS['TL_DCA'][$t]['list']['operations']['show'],
        );
    }
}
