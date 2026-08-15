<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
#[ORM\Table(name: 'site')]
#[ORM\UniqueConstraint(name: 'uniq_site_slug', columns: ['slug'])]
class Site
{
    public const MAIN_SLUG = 'main';

    /** First shipped frontend theme id (Phase 9). */
    public const DEFAULT_THEME_ID = 'default';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $slug = '';

    #[ORM\Column(length: 128)]
    private string $name = '';

    /** Frontend theme package id (`theme.json` id / `data-wh-theme`). */
    #[ORM\Column(length: 64)]
    private string $themeId = self::DEFAULT_THEME_ID;

    #[ORM\Column]
    private bool $isEnabled = true;

    #[ORM\Column]
    private bool $isProtected = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: MediaAsset::class)]
    #[ORM\JoinColumn(name: 'favicon_media_id', nullable: true, onDelete: 'SET NULL')]
    private ?MediaAsset $faviconMedia = null;

    /** @var Collection<int, SiteHost> */
    #[ORM\OneToMany(targetEntity: SiteHost::class, mappedBy: 'site', cascade: ['persist'])]
    private Collection $hosts;

    public function __construct()
    {
        $this->hosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = trim($name);

        return $this;
    }

    public function getThemeId(): string
    {
        return $this->themeId;
    }

    public function setThemeId(string $themeId): self
    {
        $this->themeId = strtolower(trim($themeId));

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function isProtected(): bool
    {
        return $this->isProtected;
    }

    public function setIsProtected(bool $isProtected): self
    {
        $this->isProtected = $isProtected;

        return $this;
    }

    public function isMain(): bool
    {
        return self::MAIN_SLUG === $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        if (null === $description) {
            $this->description = null;

            return $this;
        }

        $trimmed = trim($description);
        $this->description = '' === $trimmed ? null : $trimmed;

        return $this;
    }

    public function getFaviconMedia(): ?MediaAsset
    {
        return $this->faviconMedia;
    }

    public function setFaviconMedia(?MediaAsset $faviconMedia): self
    {
        $this->faviconMedia = $faviconMedia;

        return $this;
    }

    /** @return Collection<int, SiteHost> */
    public function getHosts(): Collection
    {
        return $this->hosts;
    }

    public function addHost(SiteHost $host): self
    {
        if (!$this->hosts->contains($host)) {
            $this->hosts->add($host);
            $host->setSite($this);
        }

        return $this;
    }
}
