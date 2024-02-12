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

use Codefog\HasteBundle\Form\Form;
use Contao\Controller;
use Contao\CoreBundle\Controller\AbstractBackendController;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\System;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Markocupic\PhpOffice\PhpWord\MsWordTemplateProcessor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class MathbuchAhLevel
{
    public const AH_BASIC = 'ah_basic';
    public const AH_PLUS = 'ah_plus';
    public const ALL = [
        self::AH_BASIC,
        self::AH_PLUS,
    ];

}
