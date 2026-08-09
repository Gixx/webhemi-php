<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * RBAC reset: empty permission catalog; only protected Admin + Site Admin roles.
 * @see docs/plan/RBAC_Reset.md
 */
final class Version20260809120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'RBAC reset: clear permissions; keep protected ROLE_ADMIN and ROLE_SITE_ADMIN only';
    }

    public function up(Schema $schema): void
    {
        // Drop assignments / user links to non-system roles first.
        $this->addSql(<<<'SQL'
            DELETE sa FROM site_assignment sa
            INNER JOIN rbac_role r ON r.id = sa.role_id
            WHERE r.name NOT IN ('ROLE_ADMIN', 'ROLE_SITE_ADMIN')
            SQL);

        $this->addSql(<<<'SQL'
            DELETE ur FROM user_role ur
            INNER JOIN rbac_role r ON r.id = ur.role_id
            WHERE r.name NOT IN ('ROLE_ADMIN', 'ROLE_SITE_ADMIN')
            SQL);

        $this->addSql('DELETE FROM role_permission');
        $this->addSql('DELETE FROM rbac_permission');

        $this->addSql(<<<'SQL'
            DELETE FROM rbac_role
            WHERE name NOT IN ('ROLE_ADMIN', 'ROLE_SITE_ADMIN')
            SQL);

        $this->addSql(<<<'SQL'
            INSERT INTO rbac_role (name, label, is_read_only)
            SELECT 'ROLE_ADMIN', 'Administrator', 1
            WHERE NOT EXISTS (SELECT 1 FROM rbac_role WHERE name = 'ROLE_ADMIN')
            SQL);

        $this->addSql(<<<'SQL'
            INSERT INTO rbac_role (name, label, is_read_only)
            SELECT 'ROLE_SITE_ADMIN', 'Site Administrator', 1
            WHERE NOT EXISTS (SELECT 1 FROM rbac_role WHERE name = 'ROLE_SITE_ADMIN')
            SQL);

        $this->addSql(<<<'SQL'
            UPDATE rbac_role
            SET is_read_only = 1,
                label = CASE name
                    WHEN 'ROLE_ADMIN' THEN 'Administrator'
                    WHEN 'ROLE_SITE_ADMIN' THEN 'Site Administrator'
                    ELSE label
                END
            WHERE name IN ('ROLE_ADMIN', 'ROLE_SITE_ADMIN')
            SQL);
    }

    public function down(Schema $schema): void
    {
        // Irreversible data cleanup — no restore of Editor / permission catalog.
        $this->addSql('SELECT 1');
    }
}
