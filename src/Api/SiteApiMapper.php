<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\MediaAsset;
use App\Entity\Site;

final class SiteApiMapper
{
    /**
     * @return array{
     *     id: int,
     *     slug: string,
     *     name: string,
     *     themeId: string,
     *     enabled: bool,
     *     protected: bool,
     *     hostCount: int,
     *     faviconMediaId: int|null
     * }
     */
    public static function toArray(Site $site): array
    {
        return [
            'id' => (int) $site->getId(),
            'slug' => $site->getSlug(),
            'name' => $site->getName(),
            'themeId' => $site->getThemeId(),
            'enabled' => $site->isEnabled(),
            'protected' => $site->isProtected(),
            'hostCount' => $site->getHosts()->count(),
            'faviconMediaId' => self::faviconMediaId($site),
        ];
    }

    private static function faviconMediaId(Site $site): ?int
    {
        $favicon = $site->getFaviconMedia();
        if (!$favicon instanceof MediaAsset || $favicon->isDeleted()) {
            return null;
        }

        return (int) $favicon->getId();
    }
}
