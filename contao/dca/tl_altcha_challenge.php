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

use Contao\DC_Table;
use Doctrine\DBAL\Types\Types;

$GLOBALS['TL_DCA']['tl_altcha_challenge'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'sql'           => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'fields' => [
        'id'        => [
            'sql' => [
                'type'          => Types::INTEGER,
                'length'        => 10,
                'unsigned'      => true,
                'notnull'       => true,
                'autoincrement' => true,
            ],
        ],
        'tstamp'    => [
            'sql' => [
                'type'     => Types::INTEGER,
                'length'   => 10,
                'unsigned' => true,
                'notnull'  => true,
                'default'  => 0,
            ],
        ],
        'challenge' => [
            'sql'  => [
                'type'    => Types::STRING,
                'length'  => 1024,
                'notnull' => true,
                'default' => '',
            ],
        ],
    ],
];
