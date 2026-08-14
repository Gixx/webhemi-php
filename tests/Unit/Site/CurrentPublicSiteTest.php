<?php

declare(strict_types=1);

namespace App\Tests\Unit\Site;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Routing\HostContext;
use App\Site\CurrentPublicSite;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CurrentPublicSiteTest extends TestCase
{
    public function testRequiresVerifiedEnabledSiteHost(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main')->setIsEnabled(true);
        $host = (new SiteHost())
            ->setHost('www.example.test')
            ->setSurface(SurfaceType::Site)
            ->setVerification('verified')
            ->setIsEnabled(true)
            ->setSite($site);

        $resolved = (new CurrentPublicSite())->require(new HostContext($host));

        self::assertSame($site, $resolved['site']);
        self::assertSame($host, $resolved['host']);
    }

    public function testUnknownHostThrows(): void
    {
        $this->expectException(NotFoundHttpException::class);
        (new CurrentPublicSite())->require(new HostContext(null));
    }

    public function testDisabledSiteThrows(): void
    {
        $site = (new Site())->setSlug('blog')->setName('Blog')->setIsEnabled(false);
        $host = (new SiteHost())
            ->setHost('blog.example.test')
            ->setSurface(SurfaceType::Site)
            ->setVerification('verified')
            ->setIsEnabled(true)
            ->setSite($site);

        $this->expectException(NotFoundHttpException::class);
        (new CurrentPublicSite())->require(new HostContext($host));
    }

    public function testAdminSurfaceThrows(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main')->setIsEnabled(true);
        $host = (new SiteHost())
            ->setHost('admin.example.test')
            ->setSurface(SurfaceType::Admin)
            ->setVerification('verified')
            ->setIsEnabled(true)
            ->setSite($site);

        $this->expectException(NotFoundHttpException::class);
        (new CurrentPublicSite())->require(new HostContext($host));
    }
}
