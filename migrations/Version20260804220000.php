<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Allow SiteHost without a site (create unassigned; assign/unassign later).
 */
final class Version20260804220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make site_host.site_id nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE site_host CHANGE site_id site_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE site_host SET site_id = (SELECT id FROM site ORDER BY id ASC LIMIT 1) WHERE site_id IS NULL');
        $this->addSql('ALTER TABLE site_host CHANGE site_id site_id INT NOT NULL');
    }
}
