<?php

declare(strict_types=1);

namespace App\Tests\Unit\Theme;

use App\Entity\Site;
use App\Theme\ThemeNotFoundException;
use App\Theme\ThemeResolver;
use App\Theme\ThemeSource;
use PHPUnit\Framework\TestCase;

final class ThemeResolverTest extends TestCase
{
    private string $projectDir;

    protected function setUp(): void
    {
        $this->projectDir = dirname(__DIR__, 3);
    }

    public function testResolvesShippedDefault(): void
    {
        $theme = (new ThemeResolver($this->projectDir))->resolve(Site::DEFAULT_THEME_ID);

        self::assertSame('default', $theme->id);
        self::assertSame(ThemeSource::Shipped, $theme->source);
        self::assertSame('WebHemi Default', $theme->manifest->name);
        self::assertSame('webhemi-php', $theme->manifest->engine);
        self::assertDirectoryExists($theme->templatesPath);
    }

    public function testUnknownThemeFallsBackToDefault(): void
    {
        $theme = (new ThemeResolver($this->projectDir))->resolve('does-not-exist');

        self::assertSame('default', $theme->id);
        self::assertSame(ThemeSource::Shipped, $theme->source);
    }

    public function testUploadedTakesPrecedenceOverShipped(): void
    {
        $id = 'default';
        $root = $this->projectDir . '/var/themes/' . $id;
        $templates = $root . '/templates';
        if (!is_dir($templates) && !mkdir($templates, 0777, true) && !is_dir($templates)) {
            self::fail('Could not create temp uploaded theme dir.');
        }

        $manifest = json_encode([
            'id' => $id,
            'name' => 'Uploaded Override',
            'version' => '9.9.9',
            'engine' => 'webhemi-php',
        ], JSON_THROW_ON_ERROR);
        file_put_contents($root . '/theme.json', $manifest);
        file_put_contents($templates . '/home.html.twig', 'uploaded');

        try {
            $theme = (new ThemeResolver($this->projectDir))->resolve($id);
            self::assertSame(ThemeSource::Uploaded, $theme->source);
            self::assertSame('Uploaded Override', $theme->manifest->name);
            self::assertSame('9.9.9', $theme->manifest->version);
        } finally {
            @unlink($templates . '/home.html.twig');
            @unlink($root . '/theme.json');
            @rmdir($templates);
            @rmdir($root);
            @rmdir($this->projectDir . '/var/themes');
        }
    }

    public function testMissingDefaultThrows(): void
    {
        $empty = sys_get_temp_dir() . '/webhemi-theme-empty-' . uniqid('', true);
        mkdir($empty);

        try {
            $this->expectException(ThemeNotFoundException::class);
            (new ThemeResolver($empty))->resolve('default');
        } finally {
            @rmdir($empty);
        }
    }
}
