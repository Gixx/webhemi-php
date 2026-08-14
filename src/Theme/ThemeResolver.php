<?php

declare(strict_types=1);

namespace App\Theme;

use App\Entity\Site;

/**
 * Resolves a Site theme id to an on-disk package.
 *
 * Precedence: valid `var/themes/<id>` (uploaded), else shipped `themes/<id>`,
 * else fall back to {@see Site::DEFAULT_THEME_ID} shipped package.
 */
final class ThemeResolver
{
    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    public function resolve(string $themeId): ThemePackage
    {
        $themeId = strtolower(trim($themeId));
        if ('' === $themeId) {
            $themeId = Site::DEFAULT_THEME_ID;
        }

        $uploaded = $this->tryLoadUploaded($themeId);
        if ($uploaded instanceof ThemePackage) {
            return $uploaded;
        }

        $shipped = $this->tryLoadShipped($themeId);
        if ($shipped instanceof ThemePackage) {
            return $shipped;
        }

        if (Site::DEFAULT_THEME_ID !== $themeId) {
            $fallback = $this->tryLoadShipped(Site::DEFAULT_THEME_ID);
            if ($fallback instanceof ThemePackage) {
                return $fallback;
            }
        }

        throw new ThemeNotFoundException(sprintf('Frontend theme "%s" is not installed.', $themeId));
    }

    private function tryLoadUploaded(string $themeId): ?ThemePackage
    {
        $root = $this->projectDir . '/var/themes/' . $themeId;
        $manifestPath = $root . '/theme.json';
        if (!is_file($manifestPath)) {
            return null;
        }

        $manifest = $this->readManifest($manifestPath);
        if (!$manifest instanceof ThemeManifest || $manifest->id !== $themeId) {
            return null;
        }

        $templates = $root . '/templates';
        if (!is_dir($templates)) {
            return null;
        }

        return new ThemePackage(
            id: $themeId,
            source: ThemeSource::Uploaded,
            rootPath: $root,
            templatesPath: $templates,
            manifest: $manifest,
        );
    }

    private function tryLoadShipped(string $themeId): ?ThemePackage
    {
        $root = $this->projectDir . '/themes/' . $themeId;
        $manifestPath = $root . '/theme.json';
        if (!is_file($manifestPath)) {
            return null;
        }

        $manifest = $this->readManifest($manifestPath);
        if (!$manifest instanceof ThemeManifest || $manifest->id !== $themeId) {
            return null;
        }

        $templates = $this->projectDir . '/templates/themes/' . $themeId;
        if (!is_dir($templates)) {
            return null;
        }

        return new ThemePackage(
            id: $themeId,
            source: ThemeSource::Shipped,
            rootPath: $root,
            templatesPath: $templates,
            manifest: $manifest,
        );
    }

    private function readManifest(string $path): ?ThemeManifest
    {
        $raw = @file_get_contents($path);
        if (false === $raw) {
            return null;
        }

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return ThemeManifest::tryFrom($data);
    }
}
