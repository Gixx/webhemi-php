<?php

declare(strict_types=1);

namespace App\Theme;

/**
 * Resolved installable frontend theme package.
 */
final readonly class ThemePackage
{
    public function __construct(
        public string $id,
        public ThemeSource $source,
        public string $rootPath,
        public string $templatesPath,
        public ThemeManifest $manifest,
    ) {
    }

    /**
     * AssetMapper / public path prefix for theme CSS (shipped under assets/themes/<id>).
     */
    public function assetPrefix(): string
    {
        return 'themes/' . $this->id;
    }

    public function isShipped(): bool
    {
        return ThemeSource::Shipped === $this->source;
    }
}
