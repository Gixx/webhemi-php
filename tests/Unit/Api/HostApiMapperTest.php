<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\HostApiMapper;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use PHPUnit\Framework\TestCase;

final class HostApiMapperTest extends TestCase
{
    public function testToArrayWithSite(): void
    {
        $site = (new Site())->setName('Main')->setSlug('main');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 4);

        $host = (new SiteHost())
            ->setSite($site)
            ->setHost('www.example.test')
            ->setSurface(SurfaceType::Admin)
            ->setVerification('pending')
            ->setIsEnabled(true);
        $hostRef = new \ReflectionProperty(SiteHost::class, 'id');
        $hostRef->setValue($host, 12);

        self::assertSame(
            [
                'id' => 12,
                'host' => 'www.example.test',
                'siteId' => 4,
                'siteSlug' => 'main',
                'siteName' => 'Main',
                'surface' => 'admin',
                'verification' => 'pending',
                'enabled' => true,
                'protected' => false,
            ],
            HostApiMapper::toArray($host),
        );
    }

    public function testToArrayWithoutSite(): void
    {
        $host = (new SiteHost())
            ->setHost('orphan.example.test')
            ->setSurface(SurfaceType::Site)
            ->setVerification('pending')
            ->setIsEnabled(true);
        $hostRef = new \ReflectionProperty(SiteHost::class, 'id');
        $hostRef->setValue($host, 3);

        self::assertSame(
            [
                'id' => 3,
                'host' => 'orphan.example.test',
                'siteId' => null,
                'siteSlug' => null,
                'siteName' => null,
                'surface' => 'site',
                'verification' => 'pending',
                'enabled' => true,
                'protected' => false,
            ],
            HostApiMapper::toArray($host),
        );
    }
}
