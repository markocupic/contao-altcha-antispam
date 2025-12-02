<?php

declare(strict_types=1);

use Contao\EasyCodingStandard\Set\SetList;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return ECSConfig::configure()
	->withSets([SetList::CONTAO])
	->withPaths([
		__DIR__ . '/../../src',
	])
	->withSkip([
		\Contao\EasyCodingStandard\Fixer\CommentLengthFixer::class          => ['*.php'],
		\PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer::class  => [
			'*/DependencyInjection/Configuration.php',
		],
	])
	->withRootFiles()
	->withParallel()
	->withSpacing(Option::INDENTATION_SPACES, "\n")
	->withConfiguredRule(HeaderCommentFixer::class, [
        'header' => "This file is part of Contao Altcha Antispam.\n\n(c) Marko Cupic ".date('Y')." <m.cupic@gmx.ch>\n@license GPL-3.0-or-later\nFor the full copyright and license information,\nplease view the LICENSE file that was distributed with this source code.\n@link https://github.com/markocupic/contao-altcha-antispam",
	])
	->withCache(sys_get_temp_dir() . '/ecs/markocupic/conato-altcha-antispam');
