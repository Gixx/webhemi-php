<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;

final class SiteApiMapper
{
    /**
     * @return array{id: int, slug: string, name: string, enabled: bool, hostCount: int}
     */
    public static function toArray(Site $site): array
    {
        return [
            'id' => (int) $site->getId(),
            'slug' => $site->getSlug(),
            'name' => $site->getName(),
            'enabled' => $site->isEnabled(),
            'hostCount' => $site->getHosts()->count(),
        ];
    }
}
