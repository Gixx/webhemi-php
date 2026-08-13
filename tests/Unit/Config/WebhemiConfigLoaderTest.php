<?php

declare(strict_types=1);

namespace App\Tests\Unit\Config;

use App\Config\AdminAccessMode;
use App\Config\WebhemiConfig;
use App\Config\WebhemiConfigLoader;
use PHPUnit\Framework\TestCase;

final class WebhemiConfigLoaderTest extends TestCase
{
    private string $projectDir;

    protected function setUp(): void
    {
        $this->projectDir = sys_get_temp_dir() . '/webhemi-config-' . bin2hex(random_bytes(8));
        mkdir($this->projectDir . '/var/config', 0775, true);
    }

    protected function tearDown(): void
    {
        $this->removeTree($this->projectDir);
    }

    public function testMissingFileReturnsPathDefaults(): void
    {
        $loader = new WebhemiConfigLoader($this->projectDir);
        $config = $loader->get();

        self::assertSame(AdminAccessMode::Path, $config->adminAccess);
        self::assertSame('/admin', $config->adminPath);
        self::assertSame('/admin/api', $config->adminApiPath);
        self::assertSame('/api', $config->publicApiPath);
        self::assertSame('/admin/api', $config->protectedApiPath());
        self::assertSame(['/api', '/login', '/register'], $config->reservedSitePaths());
    }

    public function testLoadsDomainAccessFromFile(): void
    {
        $this->writeConfig(<<<'YAML'
webhemi:
  access:
    admin: domain
  paths:
    admin: /admin
    admin_api: /admin/api
    public_api: /api
    login: /login
    register: /register
YAML);

        $loader = new WebhemiConfigLoader($this->projectDir);
        $config = $loader->get();

        self::assertSame(AdminAccessMode::Domain, $config->adminAccess);
        self::assertSame('/api', $config->protectedApiPath());
    }

    public function testRejectsInvalidAccessMode(): void
    {
        $this->writeConfig("webhemi:\n  access:\n    admin: subdomain\n");

        $loader = new WebhemiConfigLoader($this->projectDir);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('path" or "domain');
        $loader->get();
    }

    public function testRejectsRelativePath(): void
    {
        $loader = new WebhemiConfigLoader($this->projectDir);

        $this->expectException(\InvalidArgumentException::class);
        $loader->fromArray([
            'webhemi' => [
                'access' => ['admin' => 'path'],
                'paths' => ['admin' => 'admin'],
            ],
        ]);
    }

    public function testLoadsSymfonyDebugToolbarAndDefaultsTrueWhenMissing(): void
    {
        $this->writeConfig(<<<'YAML'
webhemi:
  access:
    admin: path
  paths:
    admin: /admin
    admin_api: /admin/api
    public_api: /api
    login: /login
    register: /register
YAML);

        $loader = new WebhemiConfigLoader($this->projectDir);
        self::assertTrue($loader->get()->symfonyDebugToolbar);

        $this->writeConfig(<<<'YAML'
webhemi:
  access:
    admin: path
  symfony:
    debug_toolbar: false
  paths:
    admin: /admin
    admin_api: /admin/api
    public_api: /api
    login: /login
    register: /register
YAML);

        self::assertFalse($loader->reload()->symfonyDebugToolbar);
        $loader->save($loader->get()->withSymfonyDebugToolbar(true));
        self::assertTrue($loader->reload()->symfonyDebugToolbar);
    }

    public function testSaveRoundTripAndEnsureFileExists(): void
    {
        $loader = new WebhemiConfigLoader($this->projectDir);
        self::assertFileDoesNotExist($loader->configPath());

        $written = $loader->ensureFileExists(new WebhemiConfig(
            adminAccess: AdminAccessMode::Domain,
            adminPath: '/admin',
            adminApiPath: '/admin/api',
            publicApiPath: '/api',
            loginPath: '/login',
            registerPath: '/register',
        ));

        self::assertFileExists($loader->configPath());
        self::assertSame(AdminAccessMode::Domain, $written->adminAccess);

        $loader->reload();
        self::assertSame(AdminAccessMode::Domain, $loader->get()->adminAccess);

        // Second ensure must not overwrite
        $loader->ensureFileExists(WebhemiConfig::defaults());
        self::assertSame(AdminAccessMode::Domain, $loader->reload()->adminAccess);
    }

    private function writeConfig(string $yaml): void
    {
        file_put_contents($this->projectDir . '/var/config/webhemi.yaml', $yaml);
    }

    private function removeTree(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir);
        if (false === $items) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->removeTree($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}
