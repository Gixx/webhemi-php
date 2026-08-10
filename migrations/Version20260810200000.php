<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add optional long-form help text on roles (CP Roles window).
 */
final class Version20260810200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add description column to rbac_role';
    }

    public function up(Schema $schema): void
    {
        // Empty default so existing rows stay valid before operators fill help text.
        $this->addSql("ALTER TABLE rbac_role ADD description LONGTEXT NOT NULL DEFAULT ''");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rbac_role DROP description');
    }
}
