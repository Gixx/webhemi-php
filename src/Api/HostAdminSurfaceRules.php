<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;
use App\Entity\SiteHost;

/**
 * Admin host surface is allowed only on the Main site, and only one admin host at a time.
 * Main site is identified by {@see Site::MAIN_SLUG} until an is_protected flag lands.
 */
final class HostAdminSurfaceRules
{
    public static function isMainSite(?Site $site): bool
    {
        return $site instanceof Site && $site->isMain();
    }

    /**
     * Admin surface requires a Main-site assignment (not unassigned, not other sites).
     */
    public static function allowsAdminSurface(?Site $site): bool
    {
        return self::isMainSite($site);
    }

    /**
     * Whether {@see $subjectHostId} may claim surface=admin on {@see $site}.
     * {@see $existingAdmin} is any current admin-surface host (optional exclude via id match).
     */
    public static function canClaimAdminSurface(
        ?Site $site,
        ?int $subjectHostId,
        ?SiteHost $existingAdmin,
    ): bool {
        if (!self::allowsAdminSurface($site)) {
            return false;
        }
        if (!$existingAdmin instanceof SiteHost) {
            return true;
        }

        return $existingAdmin->getId() === $subjectHostId;
    }
}
