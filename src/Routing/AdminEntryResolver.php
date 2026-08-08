<?php

declare(strict_types=1);

namespace App\Routing;

use App\Config\AdminAccessMode;
use App\Config\WebhemiConfigLoader;
use App\Entity\SiteHost;
use App\Repository\SiteHostRepository;

/**
 * Resolves effective admin access mode and canonical hosts.
 * Domain mode falls back to path when no healthy Main admin host exists (no file rewrite).
 */
final class AdminEntryResolver implements AdminEntryResolverInterface
{
    public function __construct(
        private readonly WebhemiConfigLoader $configLoader,
        private readonly SiteHostRepository $hosts,
    ) {
    }

    public function resolve(): CanonicalAdminEntry
    {
        $config = $this->configLoader->get();
        $mainSiteHost = $this->hosts->findMainSiteHost();
        $adminHost = $this->hosts->findMainAdminHost();

        $configured = $config->adminAccess;
        $effective = $configured;
        if (AdminAccessMode::Domain === $configured && !$adminHost instanceof SiteHost) {
            $effective = AdminAccessMode::Path;
        }

        return new CanonicalAdminEntry(
            effectiveMode: $effective,
            adminPath: $config->adminPath,
            adminApiPath: $config->adminApiPath,
            publicApiPath: $config->publicApiPath,
            mainSiteHost: $mainSiteHost,
            adminHost: $adminHost,
        );
    }
}
