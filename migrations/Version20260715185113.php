<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Legacy SQLite bootstrap snapshot (not used on MariaDB/MySQL — schema already exists).
 */
final class Version20260715185113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'No-op on MariaDB/MySQL; SQLite-only historical bootstrap (skipped)';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform,
            'SQLite-only bootstrap; skipped on this platform.',
        );
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform,
            'SQLite-only bootstrap; skipped on this platform.',
        );
    }
}
