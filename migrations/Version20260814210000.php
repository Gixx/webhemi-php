<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Site → frontend theme assignment (Phase 9 Hello world).
 */
final class Version20260814210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add theme_id column to site (default frontend theme)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE site ADD theme_id VARCHAR(64) NOT NULL DEFAULT 'default'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE site DROP theme_id');
    }
}
