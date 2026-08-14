<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContentNodeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContentNodeRepository::class)]
#[ORM\Table(name: 'content_node')]
#[ORM\Index(name: 'idx_content_node_site_parent', columns: ['site_id', 'parent_id', 'tree'])]
#[ORM\Index(name: 'idx_content_node_site_deleted', columns: ['site_id', 'deleted_at'])]
class ContentNode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Site::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Site $site = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?self $parent = null;

    #[ORM\Column(length: 16, enumType: ContentTree::class)]
    private ContentTree $tree = ContentTree::Site;

    #[ORM\Column(length: 16, enumType: ContentNodeKind::class)]
    private ContentNodeKind $kind = ContentNodeKind::Folder;

    #[ORM\Column(length: 16, nullable: true, enumType: FolderType::class)]
    private ?FolderType $folderType = null;

    #[ORM\Column(length: 128)]
    private string $slug = '';

    #[ORM\Column(length: 255)]
    private string $title = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $body = null;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $redirectTarget = null;

    #[ORM\ManyToOne(targetEntity: MediaAsset::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?MediaAsset $mediaAsset = null;

    #[ORM\Column(length: 16, enumType: PublicationStatus::class)]
    private PublicationStatus $publication = PublicationStatus::Draft;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishAt = null;

    #[ORM\Column]
    private bool $hidden = false;

    #[ORM\Column]
    private int $sortOrder = 0;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $deletedBy = null;

    /** Parent at soft-delete time (for restore). */
    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?self $originalParent = null;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getTree(): ContentTree
    {
        return $this->tree;
    }

    public function setTree(ContentTree $tree): self
    {
        $this->tree = $tree;

        return $this;
    }

    public function getKind(): ContentNodeKind
    {
        return $this->kind;
    }

    public function setKind(ContentNodeKind $kind): self
    {
        $this->kind = $kind;

        return $this;
    }

    public function getFolderType(): ?FolderType
    {
        return $this->folderType;
    }

    public function setFolderType(?FolderType $folderType): self
    {
        $this->folderType = $folderType;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = strtolower(trim($slug));

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = trim($title);

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getRedirectTarget(): ?string
    {
        return $this->redirectTarget;
    }

    public function setRedirectTarget(?string $redirectTarget): self
    {
        $this->redirectTarget = null === $redirectTarget ? null : trim($redirectTarget);

        return $this;
    }

    public function getMediaAsset(): ?MediaAsset
    {
        return $this->mediaAsset;
    }

    public function setMediaAsset(?MediaAsset $mediaAsset): self
    {
        $this->mediaAsset = $mediaAsset;

        return $this;
    }

    public function getPublication(): PublicationStatus
    {
        return $this->publication;
    }

    public function setPublication(PublicationStatus $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    public function getPublishAt(): ?\DateTimeImmutable
    {
        return $this->publishAt;
    }

    public function setPublishAt(?\DateTimeImmutable $publishAt): self
    {
        $this->publishAt = $publishAt;

        return $this;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

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

    public function getOriginalParent(): ?self
    {
        return $this->originalParent;
    }

    public function setOriginalParent(?self $originalParent): self
    {
        $this->originalParent = $originalParent;

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
