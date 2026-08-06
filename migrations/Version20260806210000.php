<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Host ownership status no longer uses "active"; assigned hosts stay "verified".
 */
final class Version20260806210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Map site_host.status active → verified (verification is pending|verified only)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE site_host SET status = 'verified' WHERE status = 'active'");
    }

    public function down(Schema $schema): void
    {
        // Cannot restore which verified rows were formerly "active".
        $this->addSql("UPDATE site_host SET status = 'active' WHERE status = 'verified' AND site_id IS NOT NULL");
    }
}
