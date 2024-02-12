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

/*
 * Backend modules
 */
$GLOBALS['TL_LANG']['MOD']['mathbuch'] = 'Mathbuch';
$GLOBALS['TL_LANG']['MOD']['mathbuch_chapters'] = ['Lernumgebungen bearbeiten', 'Mathbuch Lernumgebungen bearbeiten.'];
$GLOBALS['TL_LANG']['MOD']['mathbuch_learning_objectives'] = ['Kompetenzen bearbeiten', 'Mathbuch Kompetenzen bearbeiten.'];
$GLOBALS['TL_LANG']['MOD'][MathbuchObjectiveDocxExportController::BACKEND_MODULE_TYPE] = ['Kompetenzen exportieren', 'Mathbuch Kompetenzen exportieren'];
