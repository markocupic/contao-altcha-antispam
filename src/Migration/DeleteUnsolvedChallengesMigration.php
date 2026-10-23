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

namespace Markocupic\ContaoAltchaAntispam\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * Previous versions stored every requested challenge in the database. Now only
 * solved challenges are stored. Delete the unsolved legacy
 * entries before the unique index on the challenge column is created.
 */
class DeleteUnsolvedChallengesMigration extends AbstractMigration
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_altcha_challenge'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_altcha_challenge');

        if (!isset($columns['solved'])) {
            return false;
        }

        return false !== $this->connection->fetchOne("SELECT id FROM tl_altcha_challenge WHERE solved != '1' LIMIT 1");
    }

    public function run(): MigrationResult
    {
        $this->connection->executeStatement("DELETE FROM tl_altcha_challenge WHERE solved != '1'");

        return $this->createResult(true);
    }
}
