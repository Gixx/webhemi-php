<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Phase 10 Slice 3: site-interior settings fields.
 */
final class Version20260815120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add site.description and site.favicon_media_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE site ADD description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE site ADD favicon_media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_site_favicon_media FOREIGN KEY (favicon_media_id) REFERENCES media_asset (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_site_favicon_media ON site (favicon_media_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_site_favicon_media');
        $this->addSql('DROP INDEX IDX_site_favicon_media ON site');
        $this->addSql('ALTER TABLE site DROP favicon_media_id');
        $this->addSql('ALTER TABLE site DROP description');
    }
}
