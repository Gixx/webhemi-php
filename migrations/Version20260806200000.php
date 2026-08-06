<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Site delete must not cascade-destroy hosts; leave site_id NULL instead.
 */
final class Version20260806200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'site_host.site_id ON DELETE SET NULL (was CASCADE)';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$schema->hasTable('site_host'), 'site_host table missing');

        if ($this->connection->getDatabasePlatform() instanceof SQLitePlatform) {
            // SQLite rebuilds FKs from entity metadata on schema tooling; no-op for migrate.
            return;
        }

        $fks = $this->connection->createSchemaManager()->listTableForeignKeys('site_host');
        foreach ($fks as $fk) {
            if (\in_array('site_id', $fk->getLocalColumns(), true)) {
                $this->addSql(sprintf(
                    'ALTER TABLE site_host DROP FOREIGN KEY %s',
                    $fk->getName(),
                ));
            }
        }

        $this->addSql(
            'ALTER TABLE site_host ADD CONSTRAINT fk_site_host_site '
            . 'FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE SET NULL',
        );
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(!$schema->hasTable('site_host'), 'site_host table missing');

        if ($this->connection->getDatabasePlatform() instanceof SQLitePlatform) {
            return;
        }

        $fks = $this->connection->createSchemaManager()->listTableForeignKeys('site_host');
        foreach ($fks as $fk) {
            if (\in_array('site_id', $fk->getLocalColumns(), true)) {
                $this->addSql(sprintf(
                    'ALTER TABLE site_host DROP FOREIGN KEY %s',
                    $fk->getName(),
                ));
            }
        }

        $this->addSql(
            'ALTER TABLE site_host ADD CONSTRAINT fk_site_host_site '
            . 'FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE CASCADE',
        );
    }
}
