<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\AdminAccessModeResetter;
use App\Api\HostAssigner;
use App\Api\HostDeleter;
use App\Api\HostProtectedException;
use App\Api\HostUnassigner;
use App\Api\HostUpdater;
use App\Api\UpdateHostInput;
use App\Config\WebhemiConfig;
use App\Config\WebhemiConfigLoader;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class HostProtectedGuardsTest extends TestCase
{
    /** @var list<string> */
    private array $tempDirs = [];

    protected function tearDown(): void
    {
        foreach ($this->tempDirs as $dir) {
            $this->removeTree($dir);
        }
        $this->tempDirs = [];
    }

    private function resetter(): AdminAccessModeResetter
    {
        $dir = sys_get_temp_dir() . '/webhemi-prot-' . bin2hex(random_bytes(4));
        mkdir($dir . '/var/config', 0775, true);
        $this->tempDirs[] = $dir;
        $loader = new WebhemiConfigLoader($dir);
        $loader->save(WebhemiConfig::defaults());
        $hosts = $this->createStub(SiteHostRepository::class);

        return new AdminAccessModeResetter($loader, $hosts);
    }

    public function testDeleterRejectsProtectedHost(): void
    {
        $host = (new SiteHost())->setHost('www.example.test')->setIsProtected(true);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('remove');

        $this->expectException(HostProtectedException::class);
        (new HostDeleter($em, $this->resetter()))->delete($host);
    }

    public function testUnassignerRejectsProtectedHost(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $host = (new SiteHost())
            ->setHost('www.example.test')
            ->setSite($site)
            ->setIsProtected(true);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $this->expectException(HostProtectedException::class);
        (new HostUnassigner($em, $this->resetter()))->unassign($host);
    }

    public function testUpdaterRejectsDisableOnProtectedHost(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 1);

        $host = (new SiteHost())
            ->setHost('www.example.test')
            ->setSite($site)
            ->setSurface(SurfaceType::Site)
            ->setIsProtected(true)
            ->setIsEnabled(true);

        $hosts = $this->createStub(SiteHostRepository::class);
        $sites = $this->createStub(SiteRepository::class);
        $em = $this->createStub(EntityManagerInterface::class);
        $resetter = $this->resetter();

        $input = UpdateHostInput::fromPayload(['enabled' => false]);
        self::assertTrue($input->isValid());

        $this->expectException(HostProtectedException::class);
        (new HostUpdater(
            $em,
            $hosts,
            new HostUnassigner($em, $resetter),
            new HostAssigner($sites, $hosts, $em),
            $resetter,
        ))->update($host, $input);
    }

    public function testUpdaterRejectsAdminSurfaceOnProtectedHost(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 1);

        $host = (new SiteHost())
            ->setHost('www.example.test')
            ->setSite($site)
            ->setSurface(SurfaceType::Site)
            ->setIsProtected(true);

        $hosts = $this->createStub(SiteHostRepository::class);
        $sites = $this->createStub(SiteRepository::class);
        $em = $this->createStub(EntityManagerInterface::class);
        $resetter = $this->resetter();

        $input = UpdateHostInput::fromPayload(['surface' => 'admin']);
        self::assertTrue($input->isValid());

        $this->expectException(HostProtectedException::class);
        (new HostUpdater(
            $em,
            $hosts,
            new HostUnassigner($em, $resetter),
            new HostAssigner($sites, $hosts, $em),
            $resetter,
        ))->update($host, $input);
    }

    public function testUpdaterAllowsHostnameRenameOnProtectedHost(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 1);

        $host = (new SiteHost())
            ->setHost('www.example.test')
            ->setSite($site)
            ->setSurface(SurfaceType::Site)
            ->setVerification('verified')
            ->setIsProtected(true)
            ->setIsEnabled(true);
        $hostRef = new \ReflectionProperty(SiteHost::class, 'id');
        $hostRef->setValue($host, 9);

        $hosts = $this->createStub(SiteHostRepository::class);
        $hosts->method('findOneBy')->willReturn(null);
        $sites = $this->createStub(SiteRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');
        $resetter = $this->resetter();

        $input = UpdateHostInput::fromPayload(['host' => 'www.renamed.test']);
        self::assertTrue($input->isValid());

        $result = (new HostUpdater(
            $em,
            $hosts,
            new HostUnassigner($em, $resetter),
            new HostAssigner($sites, $hosts, $em),
            $resetter,
        ))->update($host, $input);

        self::assertSame('www.renamed.test', $result->host->getHost());
        self::assertTrue($result->host->isProtected());
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
