<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Rename site_host columns to match Verification / Enabled naming.
 */
final class Version20260806220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename site_host.status→verification, is_active→is_enabled';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$schema->hasTable('site_host'), 'site_host table missing');

        if ($this->connection->getDatabasePlatform() instanceof SQLitePlatform) {
            $this->addSql('ALTER TABLE site_host RENAME COLUMN status TO verification');
            $this->addSql('ALTER TABLE site_host RENAME COLUMN is_active TO is_enabled');

            return;
        }

        $this->addSql('ALTER TABLE site_host CHANGE status verification VARCHAR(16) NOT NULL');
        $this->addSql('ALTER TABLE site_host CHANGE is_active is_enabled TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(!$schema->hasTable('site_host'), 'site_host table missing');

        if ($this->connection->getDatabasePlatform() instanceof SQLitePlatform) {
            $this->addSql('ALTER TABLE site_host RENAME COLUMN verification TO status');
            $this->addSql('ALTER TABLE site_host RENAME COLUMN is_enabled TO is_active');

            return;
        }

        $this->addSql('ALTER TABLE site_host CHANGE verification status VARCHAR(16) NOT NULL');
        $this->addSql('ALTER TABLE site_host CHANGE is_enabled is_active TINYINT(1) NOT NULL');
    }
}
