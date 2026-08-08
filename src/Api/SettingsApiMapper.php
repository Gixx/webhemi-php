<?php

declare(strict_types=1);

namespace App\Api;

use App\Config\AdminAccessMode;
use App\Config\WebhemiConfig;
use App\Entity\SiteHost;

/**
 * JSON shape for GET/PATCH /admin/api/settings.
 */
final class SettingsApiMapper
{
    /**
     * @return array{
     *     adminAccess: string,
     *     effectiveAdminAccess: string,
     *     domainAvailable: bool,
     *     adminHost: array{id: int, host: string}|null,
     *     paths: array{
     *         admin: string,
     *         adminApi: string,
     *         publicApi: string,
     *         login: string,
     *         register: string
     *     },
     *     loginUrl?: string,
     *     sessionEnded?: bool
     * }
     */
    public static function toArray(
        WebhemiConfig $config,
        AdminAccessMode $effectiveAdminAccess,
        ?SiteHost $adminHost,
        ?string $loginUrl = null,
        bool $sessionEnded = false,
    ): array {
        $data = [
            'adminAccess' => $config->adminAccess->value,
            'effectiveAdminAccess' => $effectiveAdminAccess->value,
            'domainAvailable' => $adminHost instanceof SiteHost,
            'adminHost' => $adminHost instanceof SiteHost
                ? [
                    'id' => (int) $adminHost->getId(),
                    'host' => $adminHost->getHost(),
                ]
                : null,
            'paths' => [
                'admin' => $config->adminPath,
                'adminApi' => $config->adminApiPath,
                'publicApi' => $config->publicApiPath,
                'login' => $config->loginPath,
                'register' => $config->registerPath,
            ],
        ];

        if (null !== $loginUrl) {
            $data['loginUrl'] = $loginUrl;
        }
        if ($sessionEnded) {
            $data['sessionEnded'] = true;
        }

        return $data;
    }
}
