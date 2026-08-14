<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MediaAssetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaAssetRepository::class)]
#[ORM\Table(name: 'media_asset')]
#[ORM\UniqueConstraint(name: 'uniq_media_asset_site_hash', columns: ['site_id', 'content_hash'])]
#[ORM\Index(name: 'idx_media_asset_site_folder', columns: ['site_id', 'folder_node_id'])]
class MediaAsset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Site::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Site $site = null;

    /** Media-tree folder that contains this asset; null = media library root. */
    #[ORM\ManyToOne(targetEntity: ContentNode::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?ContentNode $folderNode = null;

    #[ORM\Column(length: 64)]
    private string $contentHash = '';

    /** Relative path under var/media (e.g. ab/abcdef…). */
    #[ORM\Column(length: 255)]
    private string $storageKey = '';

    #[ORM\Column(length: 128)]
    private string $mimeType = '';

    #[ORM\Column]
    private int $byteSize = 0;

    #[ORM\Column(length: 255)]
    private string $originalFilename = '';

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $deletedBy = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getFolderNode(): ?ContentNode
    {
        return $this->folderNode;
    }

    public function setFolderNode(?ContentNode $folderNode): self
    {
        $this->folderNode = $folderNode;

        return $this;
    }

    public function getContentHash(): string
    {
        return $this->contentHash;
    }

    public function setContentHash(string $contentHash): self
    {
        $this->contentHash = strtolower(trim($contentHash));

        return $this;
    }

    public function getStorageKey(): string
    {
        return $this->storageKey;
    }

    public function setStorageKey(string $storageKey): self
    {
        $this->storageKey = $storageKey;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = trim($mimeType);

        return $this;
    }

    public function getByteSize(): int
    {
        return $this->byteSize;
    }

    public function setByteSize(int $byteSize): self
    {
        $this->byteSize = $byteSize;

        return $this;
    }

    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): self
    {
        $this->originalFilename = trim($originalFilename);

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt instanceof \DateTimeImmutable;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getDeletedBy(): ?User
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?User $deletedBy): self
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function touch(): self
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
