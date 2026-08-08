<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\SiteHost;

/**
 * Host write outcome — accessModeReset when domain admin access was forced to path.
 */
final class HostMutationResult
{
    public function __construct(
        public readonly SiteHost $host,
        public readonly bool $accessModeReset = false,
    ) {
    }
}
