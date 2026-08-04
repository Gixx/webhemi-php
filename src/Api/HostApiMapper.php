<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\SiteHost;

final class HostApiMapper
{
    /**
     * @return array{
     *     id: int,
     *     host: string,
     *     siteId: int,
     *     siteSlug: string,
     *     siteName: string,
     *     surface: string,
     *     status: string,
     *     active: bool
     * }
     */
    public static function toArray(SiteHost $host): array
    {
        $site = $host->getSite();

        return [
            'id' => (int) $host->getId(),
            'host' => $host->getHost(),
            'siteId' => (int) $site->getId(),
            'siteSlug' => $site->getSlug(),
            'siteName' => $site->getName(),
            'surface' => $host->getSurface()->value,
            'status' => $host->getStatus(),
            'active' => $host->isActive(),
        ];
    }
}
