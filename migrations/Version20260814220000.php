<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Phase 10 Slice 1: content nodes + media assets.
 */
final class Version20260814220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add content_node and media_asset tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE media_asset (
            id INT AUTO_INCREMENT NOT NULL,
            site_id INT NOT NULL,
            folder_node_id INT DEFAULT NULL,
            content_hash VARCHAR(64) NOT NULL,
            storage_key VARCHAR(255) NOT NULL,
            mime_type VARCHAR(128) NOT NULL,
            byte_size INT NOT NULL,
            original_filename VARCHAR(255) NOT NULL,
            deleted_at DATETIME DEFAULT NULL,
            deleted_by_id INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX idx_media_asset_site_folder (site_id, folder_node_id),
            UNIQUE INDEX uniq_media_asset_site_hash (site_id, content_hash),
            INDEX IDX_media_asset_site (site_id),
            INDEX IDX_media_asset_folder (folder_node_id),
            INDEX IDX_media_asset_deleted_by (deleted_by_id),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('CREATE TABLE content_node (
            id INT AUTO_INCREMENT NOT NULL,
            site_id INT NOT NULL,
            parent_id INT DEFAULT NULL,
            tree VARCHAR(16) NOT NULL,
            kind VARCHAR(16) NOT NULL,
            folder_type VARCHAR(16) DEFAULT NULL,
            slug VARCHAR(128) NOT NULL,
            title VARCHAR(255) NOT NULL,
            body LONGTEXT DEFAULT NULL,
            redirect_target VARCHAR(2048) DEFAULT NULL,
            media_asset_id INT DEFAULT NULL,
            publication VARCHAR(16) NOT NULL,
            publish_at DATETIME DEFAULT NULL,
            hidden TINYINT(1) NOT NULL,
            sort_order INT NOT NULL,
            deleted_at DATETIME DEFAULT NULL,
            deleted_by_id INT DEFAULT NULL,
            original_parent_id INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX idx_content_node_site_parent (site_id, parent_id, tree),
            INDEX idx_content_node_site_deleted (site_id, deleted_at),
            INDEX IDX_content_node_site (site_id),
            INDEX IDX_content_node_parent (parent_id),
            INDEX IDX_content_node_media (media_asset_id),
            INDEX IDX_content_node_deleted_by (deleted_by_id),
            INDEX IDX_content_node_orig_parent (original_parent_id),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE media_asset ADD CONSTRAINT FK_media_asset_site FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_asset ADD CONSTRAINT FK_media_asset_folder FOREIGN KEY (folder_node_id) REFERENCES content_node (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media_asset ADD CONSTRAINT FK_media_asset_deleted_by FOREIGN KEY (deleted_by_id) REFERENCES app_user (id) ON DELETE SET NULL');

        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_content_node_site FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_content_node_parent FOREIGN KEY (parent_id) REFERENCES content_node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_content_node_media FOREIGN KEY (media_asset_id) REFERENCES media_asset (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_content_node_deleted_by FOREIGN KEY (deleted_by_id) REFERENCES app_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_content_node_orig_parent FOREIGN KEY (original_parent_id) REFERENCES content_node (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE media_asset DROP FOREIGN KEY FK_media_asset_site');
        $this->addSql('ALTER TABLE media_asset DROP FOREIGN KEY FK_media_asset_folder');
        $this->addSql('ALTER TABLE media_asset DROP FOREIGN KEY FK_media_asset_deleted_by');
        $this->addSql('ALTER TABLE content_node DROP FOREIGN KEY FK_content_node_site');
        $this->addSql('ALTER TABLE content_node DROP FOREIGN KEY FK_content_node_parent');
        $this->addSql('ALTER TABLE content_node DROP FOREIGN KEY FK_content_node_media');
        $this->addSql('ALTER TABLE content_node DROP FOREIGN KEY FK_content_node_deleted_by');
        $this->addSql('ALTER TABLE content_node DROP FOREIGN KEY FK_content_node_orig_parent');
        $this->addSql('DROP TABLE content_node');
        $this->addSql('DROP TABLE media_asset');
    }
}
