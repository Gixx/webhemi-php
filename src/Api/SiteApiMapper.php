<?php

declare(strict_types=1);

namespace App\Api;

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
     *     hostCount: int
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
        ];
    }
}
