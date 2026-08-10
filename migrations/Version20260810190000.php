<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add optional long-form help text on permissions (CP Permissions window).
 */
final class Version20260810190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add description column to rbac_permission';
    }

    public function up(Schema $schema): void
    {
        // Empty default so existing rows (if any) stay valid before operators fill help text.
        $this->addSql("ALTER TABLE rbac_permission ADD description LONGTEXT NOT NULL DEFAULT ''");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rbac_permission DROP description');
    }
}
