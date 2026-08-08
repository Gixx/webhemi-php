<?php

declare(strict_types=1);

namespace App\Api;

use App\Config\AdminAccessMode;
use App\Config\WebhemiConfigLoader;
use App\Entity\SiteHost;
use App\Repository\SiteHostRepository;

/**
 * Forces access.admin=path when domain mode is configured but no healthy Main admin host remains.
 */
final class AdminAccessModeResetter
{
    public function __construct(
        private readonly WebhemiConfigLoader $configLoader,
        private readonly SiteHostRepository $hosts,
    ) {
    }

    public function resetToPathIfNeeded(): bool
    {
        $config = $this->configLoader->get();
        if (AdminAccessMode::Domain !== $config->adminAccess) {
            return false;
        }
        if ($this->hosts->findMainAdminHost() instanceof SiteHost) {
            return false;
        }

        $this->configLoader->save($config->withAdminAccess(AdminAccessMode::Path));

        return true;
    }
}
