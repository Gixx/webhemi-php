<?php

declare(strict_types=1);

namespace App\Routing;

use App\Config\AdminAccessMode;
use App\Entity\SiteHost;

/**
 * Resolved canonical admin entry for the current install config + hosts.
 */
final readonly class CanonicalAdminEntry
{
    public function __construct(
        public AdminAccessMode $effectiveMode,
        public string $adminPath,
        public string $adminApiPath,
        public string $publicApiPath,
        public ?SiteHost $mainSiteHost,
        public ?SiteHost $adminHost,
    ) {
    }

    public function hasCanonicalTarget(): bool
    {
        if (AdminAccessMode::Domain === $this->effectiveMode) {
            return $this->adminHost instanceof SiteHost;
        }

        return $this->mainSiteHost instanceof SiteHost;
    }

    public function canonicalHostname(): ?string
    {
        if (AdminAccessMode::Domain === $this->effectiveMode) {
            return $this->adminHost?->getHost();
        }

        return $this->mainSiteHost?->getHost();
    }
}
