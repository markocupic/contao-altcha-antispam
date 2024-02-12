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

use Markocupic\MathbuchLearningObjectives\Controller\MathbuchObjectiveDocxExportController;
use Markocupic\MathbuchLearningObjectives\Model\MathbuchChaptersModel;
use Markocupic\MathbuchLearningObjectives\Model\MathbuchLearningObjectivesModel;

/*
 * Backend modules
 */
$GLOBALS['BE_MOD'][MathbuchObjectiveDocxExportController::BACKEND_MODULE_CATEGORY]['mathbuch_chapters'] = [
    'tables' => ['tl_mathbuch_chapters'],
];

$GLOBALS['BE_MOD'][MathbuchObjectiveDocxExportController::BACKEND_MODULE_CATEGORY]['mathbuch_learning_objectives'] = [
    'tables' => ['tl_mathbuch_learning_objectives'],
];

$GLOBALS['BE_MOD'][MathbuchObjectiveDocxExportController::BACKEND_MODULE_CATEGORY][MathbuchObjectiveDocxExportController::BACKEND_MODULE_TYPE] = [
    'hideInNavigation' => true,
];

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_mathbuch_learning_objectives'] = MathbuchLearningObjectivesModel::class;
$GLOBALS['TL_MODELS']['tl_mathbuch_chapters'] = MathbuchChaptersModel::class;
