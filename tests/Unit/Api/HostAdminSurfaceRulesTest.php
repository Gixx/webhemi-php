<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\HostAdminSurfaceNotAllowedException;
use App\Api\HostAdminSurfaceRules;
use App\Api\HostAssigner;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class HostAdminSurfaceRulesTest extends TestCase
{
    public function testUnassignedRejectsAdmin(): void
    {
        self::assertFalse(HostAdminSurfaceRules::allowsAdminSurface(null));
    }

    public function testMainSiteAllowsAdmin(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main site');
        self::assertTrue(HostAdminSurfaceRules::isMainSite($site));
        self::assertTrue(HostAdminSurfaceRules::allowsAdminSurface($site));
    }

    public function testOtherSiteRejectsAdmin(): void
    {
        $site = (new Site())->setSlug('blog')->setName('Blog');
        self::assertFalse(HostAdminSurfaceRules::allowsAdminSurface($site));
    }

    public function testCanClaimWhenNoExistingAdmin(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main site');
        self::assertTrue(HostAdminSurfaceRules::canClaimAdminSurface($site, 3, null));
    }

    public function testCanClaimWhenSubjectIsExistingAdmin(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main site');
        $admin = (new SiteHost())->setHost('admin.example.test')->setSurface(SurfaceType::Admin);
        $ref = new \ReflectionProperty(SiteHost::class, 'id');
        $ref->setValue($admin, 9);

        self::assertTrue(HostAdminSurfaceRules::canClaimAdminSurface($site, 9, $admin));
        self::assertFalse(HostAdminSurfaceRules::canClaimAdminSurface($site, 4, $admin));
    }

    public function testAssignRejectsAdminSurfaceToNonMain(): void
    {
        $host = (new SiteHost())
            ->setHost('admin.other.test')
            ->setSurface(SurfaceType::Admin)
            ->setVerification('verified');
        $site = (new Site())->setSlug('blog')->setName('Blog');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 7);

        $sites = $this->createMock(SiteRepository::class);
        $sites->expects(self::once())->method('find')->with(7)->willReturn($site);
        $hosts = $this->createStub(SiteHostRepository::class);
        $hosts->method('findAdminSurfaceHost')->willReturn(null);
        $em = $this->createStub(EntityManagerInterface::class);

        $this->expectException(HostAdminSurfaceNotAllowedException::class);
        (new HostAssigner($sites, $hosts, $em))->assign($host, 7);
    }

    public function testAssignAllowsAdminSurfaceToMain(): void
    {
        $host = (new SiteHost())
            ->setHost('admin.main.test')
            ->setSurface(SurfaceType::Admin)
            ->setVerification('verified');
        $hostRef = new \ReflectionProperty(SiteHost::class, 'id');
        $hostRef->setValue($host, 2);

        $site = (new Site())->setSlug('main')->setName('Main site');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 1);

        $sites = $this->createMock(SiteRepository::class);
        $sites->expects(self::once())->method('find')->with(1)->willReturn($site);
        $hosts = $this->createStub(SiteHostRepository::class);
        $hosts->method('findAdminSurfaceHost')->willReturn(null);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $updated = (new HostAssigner($sites, $hosts, $em))->assign($host, 1);
        self::assertSame($site, $updated->getSite());
    }

    public function testAssignRejectsSecondAdminSurfaceOnMain(): void
    {
        $host = (new SiteHost())
            ->setHost('admin2.main.test')
            ->setSurface(SurfaceType::Admin)
            ->setVerification('verified');
        $hostRef = new \ReflectionProperty(SiteHost::class, 'id');
        $hostRef->setValue($host, 5);

        $existing = (new SiteHost())
            ->setHost('admin.main.test')
            ->setSurface(SurfaceType::Admin);
        $existingRef = new \ReflectionProperty(SiteHost::class, 'id');
        $existingRef->setValue($existing, 2);

        $site = (new Site())->setSlug('main')->setName('Main site');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 1);

        $sites = $this->createMock(SiteRepository::class);
        $sites->expects(self::once())->method('find')->with(1)->willReturn($site);
        $hosts = $this->createStub(SiteHostRepository::class);
        $hosts->method('findAdminSurfaceHost')->willReturn($existing);
        $em = $this->createStub(EntityManagerInterface::class);

        $this->expectException(HostAdminSurfaceNotAllowedException::class);
        (new HostAssigner($sites, $hosts, $em))->assign($host, 1);
    }
}
