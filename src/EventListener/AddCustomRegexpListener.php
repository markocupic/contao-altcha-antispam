<?php

declare(strict_types=1);

/*
 * This file is part of Contao Altcha Antispam.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-altcha-antispam
 */

namespace Markocupic\ContaoAltchaAntispam\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Validator;
use Contao\Widget;
use Symfony\Contracts\Translation\TranslatorInterface;

class AddCustomRegexpListener
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly int $altchaRangeMax,
    ) {
    }

    #[AsHook('addCustomRegexp')]
    public function validateMaxIteration(string $regexp, $input, Widget $widget): bool
    {
        if ('altcha_max_iteration' !== $regexp) {
            return false;
        }

        if (!Validator::isNatural($input)) {
            $widget->addError($this->translator->trans('ERR.natural', [], 'contao_default'));

            return true;
        }

        if ($this->altchaRangeMax > $input) {
            $widget->addError($this->translator->trans('ERR.altcha_max_iteration_range', [$this->altchaRangeMax], 'contao_default'));
        }

        return true;
    }
}
