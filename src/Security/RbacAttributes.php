<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Interim attribute matrix for Site Admin vs Admin-only actions.
 * @see docs/plan/RBAC_Reset.md
 */
final class RbacAttributes
{
    /** Prefixes only ROLE_ADMIN may use (Control Panel / hosts / install). */
    private const ADMIN_ONLY_PREFIXES = [
        'host.',
        'settings.',
        'user.',
        'role.',
        'permission.',
    ];

    /** Exact attributes reserved to ROLE_ADMIN (site identity / destroy). */
    private const ADMIN_ONLY_EXACT = [
        'site.edit',
        'site.delete',
    ];

    public static function isAdminOnly(string $attribute): bool
    {
        $normalized = strtolower(trim($attribute));
        if (in_array($normalized, self::ADMIN_ONLY_EXACT, true)) {
            return true;
        }
        foreach (self::ADMIN_ONLY_PREFIXES as $prefix) {
            if (str_starts_with($normalized, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Site-interior actions ROLE_SITE_ADMIN may perform on an assigned site
     * (content / future site settings / list-view). Not Admin-only.
     */
    public static function isSiteInterior(string $attribute): bool
    {
        return !self::isAdminOnly($attribute);
    }
}
