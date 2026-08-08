<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SiteHostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteHostRepository::class)]
#[ORM\Table(name: 'site_host')]
#[ORM\UniqueConstraint(name: 'uniq_site_host_host', columns: ['host'])]
class SiteHost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Site::class, inversedBy: 'hosts')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Site $site = null;

    #[ORM\Column(length: 191)]
    private string $host = '';

    #[ORM\Column(length: 16, enumType: SurfaceType::class)]
    private SurfaceType $surface = SurfaceType::Site;

    /** Ownership probe: pending | verified. */
    #[ORM\Column(length: 16)]
    private string $verification = 'pending';

    /** Kill switch (Enabled / Disabled), same idea as Site::$isEnabled. */
    #[ORM\Column]
    private bool $isEnabled = true;

    /** Installer/seed primary www (site-surface) host — not deletable/disableable. */
    #[ORM\Column]
    private bool $isProtected = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = strtolower(trim($host));

        return $this;
    }

    public function getSurface(): SurfaceType
    {
        return $this->surface;
    }

    public function setSurface(SurfaceType $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getVerification(): string
    {
        return $this->verification;
    }

    public function setVerification(string $verification): self
    {
        if (!\in_array($verification, ['pending', 'verified'], true)) {
            throw new \InvalidArgumentException(sprintf('Invalid host verification "%s".', $verification));
        }
        $this->verification = $verification;

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
}
