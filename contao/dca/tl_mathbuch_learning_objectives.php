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

use Contao\DC_Table;
use Contao\DataContainer;
use Doctrine\DBAL\Types\Types;
use Markocupic\MathbuchLearningObjectives\Config\MathbuchVolume;

/**
 * Table tl_mathbuch_learning_objectives
 */
$GLOBALS['TL_DCA']['tl_mathbuch_learning_objectives'] = [
    'config'      => [
        'dataContainer'    => DC_Table::class,
        'enableVersioning' => true,
        'closed'           => true,
        'notEditable'      => true,
        'notDeletable'     => true,
        'notSortable'      => true,
        'notCopyable'      => true,
        'notCreatable'     => true,
        'sql'              => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list'        => [
        'sorting'           => [
            'mode'        => DataContainer::MODE_SORTABLE,
            'fields'      => ['volume ASC ', 'belongs_to_chapter', 'id'],
            'flag'        => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label'             => [
            'fields' => ['volume', 'belongs_to_chapter', 'level_basic', 'level_plus', 'extended_objective_basic', 'extended_objective_plus', 'objective_text'],
            'format' => '%s | %s | %s | %s | %s | %s | %s',
        ],
        'global_operations' => [
            'all' => [
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'copy'   => [
                'href' => 'act=copy',
                'icon' => 'copy.svg',
            ],
            'delete' => [
                'href'       => 'act=delete',
                'icon'       => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'href'       => 'act=show',
                'icon'       => 'show.svg',
                'attributes' => 'style="margin-right:3px"',
            ],
        ],
    ],
    'palettes'    => [
        '__selector__' => ['addSubpalette'],
        'default'      => '
        {basic_legend},volume,belongs_to_chapter,chapter;
        {basic_objectives_legend},level_basic,level_plus;
        {extended_objectives_legend},extended_objective_basic,extended_objective_plus;
        {objective_text_legend},objective_text
        ',
    ],
    'subpalettes' => [
        'addSubpalette' => 'textareaField',
    ],
    'fields'      => [
        'id'                       => [
            'sql' => [
                'type'          => Types::INTEGER,
                'length'        => 10,
                'unsigned'      => true,
                'notnull'       => true,
                'autoincrement' => true,
            ],
        ],
        'tstamp'                   => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'volume'                   => [
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'options'   => MathbuchVolume::ALL,
            'reference' => &$GLOBALS['TL_LANG']['MSC']['mathbuch_volumes'],
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'      => ['mandatory' => true, 'maxlength' => 6, 'tl_class' => 'w50'],
            'sql'       => [
                'type'    => Types::STRING,
                'length'  => 32,
                'notnull' => true,
                'default' => '',
            ],
        ],
        'belongs_to_chapter'       => [
            'exclude'    => true,
            'search'     => true,
            'filter'     => true,
            'sorting'    => true,
            'flag'       => DataContainer::SORT_ASC,
            'inputType'  => 'select',
            'foreignKey' => 'tl_mathbuch_chapters.alias',
            'eval'       => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'        => [
                'type'     => Types::INTEGER,
                'length'   => 10,
                'unsigned' => true,
                'notnull'  => true,
                'default'  => 0,
            ],
            'relation'   => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
        'level_basic'              => [
            'inputType' => 'checkbox',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'      => ['mandatory' => false, 'tl_class' => 'w50'],
            'sql'       => [
                'type'    => Types::STRING,
                'length'  => 1,
                'fixed'   => true,
                'notnull' => true,
                'default' => '',
            ],
        ],
        'level_plus'               => [
            'inputType' => 'checkbox',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'      => ['mandatory' => false, 'tl_class' => 'w50'],
            'sql'       => [
                'type'    => Types::STRING,
                'length'  => 1,
                'fixed'   => true,
                'notnull' => true,
                'default' => '',
            ],
        ],
        'extended_objective_basic' => [
            'inputType' => 'checkbox',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'      => ['mandatory' => false, 'tl_class' => 'w50'],
            'sql'       => [
                'type'    => Types::STRING,
                'length'  => 1,
                'fixed'   => true,
                'notnull' => true,
                'default' => '',
            ],
        ],
        'extended_objective_plus'  => [
            'inputType' => 'checkbox',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'      => ['mandatory' => false, 'tl_class' => 'w50'],
            'sql'       => [
                'type'    => Types::STRING,
                'length'  => 1,
                'fixed'   => true,
                'notnull' => true,
                'default' => '',
            ],
        ],
        'objective_text'           => [
            'search'    => true,
            'inputType' => 'textarea',
            'eval'      => ['mandatory' => true, 'basicEntities' => true, 'helpwizard' => true],
            'sql'       => [
                'type'    => Types::TEXT,
                'notnull' => false,
            ],
        ],
    ],
];
