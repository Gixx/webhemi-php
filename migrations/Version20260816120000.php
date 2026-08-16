<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * My Account profile fields + user links.
 */
final class Version20260816120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add app_user profile fields and app_user_link table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_user ADD display_name VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD telephone VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD zip VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD city VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD country VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD bio LONGTEXT DEFAULT NULL');

        $this->addSql(<<<'SQL'
            CREATE TABLE app_user_link (
                id INT AUTO_INCREMENT NOT NULL,
                user_id INT NOT NULL,
                name VARCHAR(128) NOT NULL,
                url VARCHAR(2048) NOT NULL,
                position INT NOT NULL,
                INDEX IDX_app_user_link_user (user_id),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        $this->addSql('ALTER TABLE app_user_link ADD CONSTRAINT FK_app_user_link_user FOREIGN KEY (user_id) REFERENCES app_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_user_link DROP FOREIGN KEY FK_app_user_link_user');
        $this->addSql('DROP TABLE app_user_link');
        $this->addSql('ALTER TABLE app_user DROP bio, DROP country, DROP city, DROP zip, DROP address, DROP telephone, DROP display_name');
    }
}
