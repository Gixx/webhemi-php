<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Seed protected ROLE_GUEST; backfill empty display names for Users required Name.
 */
final class Version20260816140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add protected ROLE_GUEST; ensure app_user.display_name is non-null';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO rbac_role (name, label, description, is_read_only)
            SELECT 'ROLE_GUEST', 'Guest', '', 1
            WHERE NOT EXISTS (SELECT 1 FROM rbac_role WHERE name = 'ROLE_GUEST')
            SQL);

        $this->addSql(<<<'SQL'
            UPDATE rbac_role
            SET label = 'Guest', is_read_only = 1
            WHERE name = 'ROLE_GUEST'
            SQL);

        $this->addSql(<<<'SQL'
            UPDATE app_user
            SET display_name = email
            WHERE display_name IS NULL OR TRIM(display_name) = ''
            SQL);

        $this->addSql('ALTER TABLE app_user CHANGE display_name display_name VARCHAR(128) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_user CHANGE display_name display_name VARCHAR(128) DEFAULT NULL');

        $this->addSql(<<<'SQL'
            DELETE ur FROM user_role ur
            INNER JOIN rbac_role r ON r.id = ur.role_id
            WHERE r.name = 'ROLE_GUEST'
            SQL);
        $this->addSql("DELETE FROM rbac_role WHERE name = 'ROLE_GUEST'");
    }
}
