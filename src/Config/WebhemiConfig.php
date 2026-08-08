<?php

declare(strict_types=1);

namespace App\Config;

/**
 * Install-global WebHemi settings (var/config/webhemi.yaml).
 *
 * @see docs/plan/Admin_API_Access_Mode.md in the hub repo
 */
final readonly class WebhemiConfig
{
    public function __construct(
        public AdminAccessMode $adminAccess,
        public string $adminPath,
        public string $adminApiPath,
        public string $publicApiPath,
        public string $loginPath,
        public string $registerPath,
    ) {
    }

    public static function defaults(): self
    {
        return new self(
            adminAccess: AdminAccessMode::Path,
            adminPath: '/admin',
            adminApiPath: '/admin/api',
            publicApiPath: '/api',
            loginPath: '/login',
            registerPath: '/register',
        );
    }

    /**
     * Protected admin API path for the current access mode.
     * Path mode: under the site host (/admin/api). Domain mode: /api on the admin host.
     */
    public function protectedApiPath(): string
    {
        return $this->adminAccess === AdminAccessMode::Domain ? '/api' : $this->adminApiPath;
    }

    /**
     * @return list<string>
     */
    public function reservedSitePaths(): array
    {
        return [
            $this->publicApiPath,
            $this->loginPath,
            $this->registerPath,
        ];
    }

    /**
     * @return array{
     *     webhemi: array{
     *         access: array{admin: string},
     *         paths: array{
     *             admin: string,
     *             admin_api: string,
     *             public_api: string,
     *             login: string,
     *             register: string
     *         }
     *     }
     * }
     */
    public function toArray(): array
    {
        return [
            'webhemi' => [
                'access' => [
                    'admin' => $this->adminAccess->value,
                ],
                'paths' => [
                    'admin' => $this->adminPath,
                    'admin_api' => $this->adminApiPath,
                    'public_api' => $this->publicApiPath,
                    'login' => $this->loginPath,
                    'register' => $this->registerPath,
                ],
            ],
        ];
    }
}
