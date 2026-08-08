<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Drop obsolete host surface "api" (public API is always path /api on site hosts).
 */
final class Version20260808120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrate site_host.surface api → site (api host surface removed)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE site_host SET surface = 'site' WHERE surface = 'api'");
    }

    public function down(Schema $schema): void
    {
        // Irreversible: former api rows cannot be distinguished from site.
    }
}
