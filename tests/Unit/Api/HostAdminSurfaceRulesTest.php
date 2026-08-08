<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\HostAdminSurfaceNotAllowedException;
use App\Api\HostAdminSurfaceRules;
use App\Api\HostAssigner;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class HostAdminSurfaceRulesTest extends TestCase
{
    public function testUnassignedAllowsAdmin(): void
    {
        self::assertTrue(HostAdminSurfaceRules::allowsAdminSurface(null));
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
        $em = $this->createStub(EntityManagerInterface::class);

        $this->expectException(HostAdminSurfaceNotAllowedException::class);
        (new HostAssigner($sites, $em))->assign($host, 7);
    }

    public function testAssignAllowsAdminSurfaceToMain(): void
    {
        $host = (new SiteHost())
            ->setHost('admin.main.test')
            ->setSurface(SurfaceType::Admin)
            ->setVerification('verified');
        $site = (new Site())->setSlug('main')->setName('Main site');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 1);

        $sites = $this->createMock(SiteRepository::class);
        $sites->expects(self::once())->method('find')->with(1)->willReturn($site);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $updated = (new HostAssigner($sites, $em))->assign($host, 1);
        self::assertSame($site, $updated->getSite());
    }
}
