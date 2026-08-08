<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\AdminAccessModeResetter;
use App\Api\UpdateSettingsInput;
use App\Config\AdminAccessMode;
use App\Config\WebhemiConfig;
use App\Config\WebhemiConfigLoader;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteHostRepository;
use PHPUnit\Framework\TestCase;

final class SettingsAccessModeTest extends TestCase
{
    public function testUpdateSettingsInputAcceptsPathAndDomain(): void
    {
        $path = UpdateSettingsInput::fromPayload(['adminAccess' => 'path']);
        self::assertTrue($path->isValid());
        self::assertSame(AdminAccessMode::Path, $path->adminAccess);

        $domain = UpdateSettingsInput::fromPayload(['adminAccess' => 'DOMAIN']);
        self::assertTrue($domain->isValid());
        self::assertSame(AdminAccessMode::Domain, $domain->adminAccess);
    }

    public function testUpdateSettingsInputRejectsInvalidMode(): void
    {
        $input = UpdateSettingsInput::fromPayload(['adminAccess' => 'subdomain']);
        self::assertFalse($input->isValid());
        self::assertArrayHasKey('adminAccess', $input->fieldErrors);
    }

    public function testResetterWritesPathWhenDomainHostMissing(): void
    {
        $dir = sys_get_temp_dir() . '/webhemi-settings-' . bin2hex(random_bytes(4));
        mkdir($dir . '/var/config', 0775, true);
        $loader = new WebhemiConfigLoader($dir);
        $defaults = WebhemiConfig::defaults();
        $loader->save(new WebhemiConfig(
            adminAccess: AdminAccessMode::Domain,
            adminPath: $defaults->adminPath,
            adminApiPath: $defaults->adminApiPath,
            publicApiPath: $defaults->publicApiPath,
            loginPath: $defaults->loginPath,
            registerPath: $defaults->registerPath,
        ));

        $hosts = $this->createStub(SiteHostRepository::class);
        $hosts->method('findMainAdminHost')->willReturn(null);

        $reset = (new AdminAccessModeResetter($loader, $hosts))->resetToPathIfNeeded();
        self::assertTrue($reset);
        self::assertSame(AdminAccessMode::Path, $loader->reload()->adminAccess);

        $this->removeTree($dir);
    }

    public function testResetterNoOpWhenHealthyAdminHostExists(): void
    {
        $dir = sys_get_temp_dir() . '/webhemi-settings-' . bin2hex(random_bytes(4));
        mkdir($dir . '/var/config', 0775, true);
        $loader = new WebhemiConfigLoader($dir);
        $defaults = WebhemiConfig::defaults();
        $loader->save(new WebhemiConfig(
            adminAccess: AdminAccessMode::Domain,
            adminPath: $defaults->adminPath,
            adminApiPath: $defaults->adminApiPath,
            publicApiPath: $defaults->publicApiPath,
            loginPath: $defaults->loginPath,
            registerPath: $defaults->registerPath,
        ));

        $site = (new Site())->setSlug(Site::MAIN_SLUG)->setName('Main');
        $admin = (new SiteHost())
            ->setHost('admin.example.test')
            ->setSurface(SurfaceType::Admin)
            ->setVerification('verified')
            ->setIsEnabled(true)
            ->setSite($site);

        $hosts = $this->createStub(SiteHostRepository::class);
        $hosts->method('findMainAdminHost')->willReturn($admin);

        $reset = (new AdminAccessModeResetter($loader, $hosts))->resetToPathIfNeeded();
        self::assertFalse($reset);
        self::assertSame(AdminAccessMode::Domain, $loader->reload()->adminAccess);

        $this->removeTree($dir);
    }

    private function removeTree(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->removeTree($path) : unlink($path);
        }
        rmdir($dir);
    }
}
