<?php

declare(strict_types=1);

namespace App\Routing;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;

final class HostContext
{
    public function __construct(
        private readonly ?SiteHost $siteHost,
        private readonly ?SurfaceType $surfaceOverride = null,
    ) {
    }

    public function getSiteHost(): ?SiteHost
    {
        return $this->siteHost;
    }

    public function getSite(): ?Site
    {
        return $this->siteHost?->getSite();
    }

    public function getSurface(): SurfaceType
    {
        return $this->surfaceOverride
            ?? $this->siteHost?->getSurface()
            ?? SurfaceType::Site;
    }

    public function isResolved(): bool
    {
        return $this->siteHost instanceof SiteHost;
    }

    public function withSurfaceOverride(?SurfaceType $surfaceOverride): self
    {
        return new self($this->siteHost, $surfaceOverride);
    }
}
