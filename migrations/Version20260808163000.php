<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Protected Main site + primary www (site-surface) host flags.
 */
final class Version20260808163000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add is_protected to site and site_host; backfill Main + primary www host';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE site ADD is_protected TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE site_host ADD is_protected TINYINT(1) DEFAULT 0 NOT NULL');

        $this->addSql("UPDATE site SET is_protected = 1 WHERE slug = 'main'");

        // Prefer www.* site-surface host per main site; else lowest id (MySQL-safe).
        $this->addSql(<<<'SQL'
            UPDATE site_host h
            INNER JOIN (
                SELECT MIN(h2.id) AS id
                FROM site_host h2
                INNER JOIN site s ON s.id = h2.site_id
                WHERE s.slug = 'main'
                  AND h2.surface = 'site'
                  AND h2.host LIKE 'www.%'
                GROUP BY s.id
            ) www ON www.id = h.id
            SET h.is_protected = 1
            SQL);

        $this->addSql(<<<'SQL'
            UPDATE site_host h
            INNER JOIN (
                SELECT MIN(h2.id) AS id
                FROM site_host h2
                INNER JOIN site s ON s.id = h2.site_id
                WHERE s.slug = 'main'
                  AND h2.surface = 'site'
                  AND NOT EXISTS (
                      SELECT 1
                      FROM site_host p
                      WHERE p.site_id = s.id
                        AND p.surface = 'site'
                        AND p.is_protected = 1
                  )
                GROUP BY s.id
            ) fallback ON fallback.id = h.id
            SET h.is_protected = 1
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE site_host DROP is_protected');
        $this->addSql('ALTER TABLE site DROP is_protected');
    }
}
