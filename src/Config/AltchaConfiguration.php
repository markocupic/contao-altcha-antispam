<?php

declare(strict_types=1);

/*
 * This file is part of Contao Altcha Antispam.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-altcha-antispam
 */

namespace Markocupic\ContaoAltchaAntispam\Config;

class AltchaConfiguration
{
    public const ALGORITHM_SHA_256 = 'SHA-256';
    public const ALGORITHM_SHA_384 = 'SHA-384';
    public const ALGORITHM_SHA_512 = 'SHA-512';
    public const ALGORITHM_ALL = [
        self::ALGORITHM_SHA_256,
        self::ALGORITHM_SHA_384,
        self::ALGORITHM_SHA_512,
    ];
}
