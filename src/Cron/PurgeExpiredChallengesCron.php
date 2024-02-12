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

namespace Markocupic\ContaoAltchaAntispam\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

#[AsCronJob('hourly')]
class PurgeExpiredChallengesCron
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function __invoke(): void
    {
        // 1 week
        $limit = time() - 60 * 60 * 24 * 7;

        $this->connection->executeStatement(
            'DELETE FROM tl_altcha_challenge WHERE tstamp < :limit',
            ['limit' => $limit],
            ['limit' => Types::INTEGER],
        );
    }
}
