<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;

/**
 * Admin host surface is allowed only when unassigned or bound to the Main site.
 * Main site is identified by slug {@see self::MAIN_SLUG} until an is_protected flag lands.
 */
final class HostAdminSurfaceRules
{
    public const MAIN_SLUG = 'main';

    public static function isMainSite(?Site $site): bool
    {
        return $site instanceof Site && self::MAIN_SLUG === $site->getSlug();
    }

    /**
     * Unassigned hosts may use surface=admin; assignment must target Main.
     */
    public static function allowsAdminSurface(?Site $site): bool
    {
        return !$site instanceof Site || self::isMainSite($site);
    }
}
