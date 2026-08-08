<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\AdminAccessModeResetter;
use App\Api\HostDeleter;
use App\Config\AdminAccessMode;
use App\Config\WebhemiConfig;
use App\Config\WebhemiConfigLoader;
use App\Entity\SiteHost;
use App\Repository\SiteHostRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class HostDeleterTest extends TestCase
{
    public function testDeletesHostWithoutAccessResetWhenAlreadyPath(): void
    {
        $host = (new SiteHost())->setHost('gone.example.test');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('remove')->with($host);
        $em->expects(self::once())->method('flush');

        $dir = sys_get_temp_dir() . '/webhemi-del-' . bin2hex(random_bytes(4));
        mkdir($dir . '/var/config', 0775, true);
        $loader = new WebhemiConfigLoader($dir);
        $loader->save(WebhemiConfig::defaults());
        $hosts = $this->createStub(SiteHostRepository::class);
        $resetter = new AdminAccessModeResetter($loader, $hosts);

        $reset = (new HostDeleter($em, $resetter))->delete($host);

        self::assertFalse($reset);
        $this->removeTree($dir);
    }

    public function testDeletesHostAndResetsDomainAccessWhenAdminGone(): void
    {
        $host = (new SiteHost())->setHost('admin.example.test');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('remove')->with($host);
        $em->expects(self::once())->method('flush');

        $dir = sys_get_temp_dir() . '/webhemi-del-' . bin2hex(random_bytes(4));
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
        $resetter = new AdminAccessModeResetter($loader, $hosts);

        $reset = (new HostDeleter($em, $resetter))->delete($host);

        self::assertTrue($reset);
        self::assertSame(AdminAccessMode::Path, $loader->reload()->adminAccess);
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
