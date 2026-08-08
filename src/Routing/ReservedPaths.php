<?php

declare(strict_types=1);

namespace App\Routing;

use App\Config\WebhemiConfigLoader;

/**
 * Reserved URL prefixes (site explorer / CMS tree must not use these).
 */
final class ReservedPaths
{
    public function __construct(
        private readonly WebhemiConfigLoader $configLoader,
    ) {
    }

    /**
     * @return list<string>
     */
    public function siteHostPrefixes(): array
    {
        return $this->configLoader->get()->reservedSitePaths();
    }

    /**
     * True when $path equals or is under a reserved prefix (site host).
     */
    public function isReservedOnSiteHost(string $path): bool
    {
        $path = $this->normalize($path);
        foreach ($this->siteHostPrefixes() as $prefix) {
            if ($this->matchesPrefix($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function isAdminPath(string $path): bool
    {
        return $this->matchesPrefix(
            $this->normalize($path),
            $this->configLoader->get()->adminPath,
        );
    }

    private function matchesPrefix(string $path, string $prefix): bool
    {
        $prefix = $this->normalize($prefix);
        if ($path === $prefix) {
            return true;
        }

        return str_starts_with($path, $prefix . '/');
    }

    private function normalize(string $path): string
    {
        if ($path === '' || !str_starts_with($path, '/')) {
            $path = '/' . $path;
        }
        if (strlen($path) > 1) {
            $path = rtrim($path, '/');
        }

        return $path === '' ? '/' : $path;
    }
}
