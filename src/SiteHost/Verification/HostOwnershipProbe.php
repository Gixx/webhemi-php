<?php

declare(strict_types=1);

namespace App\SiteHost\Verification;

/**
 * Probe whether a hostname resolves to this install (file + token).
 */
interface HostOwnershipProbe
{
    public function verify(string $host): HostVerificationResult;
}
