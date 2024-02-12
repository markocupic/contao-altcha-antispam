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

namespace Markocupic\ContaoAltchaAntispam\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\Message;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormField
{
    public function __construct(
        private readonly Connection $connection,
        private readonly ContaoFramework $framework,
        private readonly RequestStack $requestStack,
        private readonly Security $security,
        private readonly TranslatorInterface $translator,
        private readonly string $altchaHmacKey,
    ) {
    }

    #[AsCallback(table: 'tl_form_field', target: 'config.onload', priority: 100)]
    public function onload(DataContainer $dc): void
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $qb = $this->connection->createQueryBuilder();

        $type = $qb->select('t.type')
            ->from('tl_form_field', 't')
            ->where('id = ? AND type = ?')
            ->setParameters([$dc->id, 'altcha_hidden'])
            ->fetchOne()
        ;

        if ('altcha_hidden' === $type && '' === $this->altchaHmacKey) {
            $message = $this->framework->getAdapter(Message::class);
            $err = $this->translator->trans('ERR.altcha_hmac_key_not_found', [], 'contao_default');
            $message->addError($err);
        }
    }
}
