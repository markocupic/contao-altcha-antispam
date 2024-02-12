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

namespace Markocupic\MathbuchLearningObjectives\Config;

class MathbuchVolume
{
    public const VOLUME_1 = '1';
    public const VOLUME_2 = '2';
    public const VOLUME_3 = '3';
    public const VOLUME_3_PLUS = '3_plus';
    public const ALL = [
        self::VOLUME_1,
        self::VOLUME_2,
        self::VOLUME_3,
        self::VOLUME_3_PLUS,
    ];
}
