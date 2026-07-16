<?php

declare(strict_types=1);

namespace App\Tests\Unit\SiteHost;

use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use PHPUnit\Framework\TestCase;

final class SiteHostStatusTest extends TestCase
{
    public function testRejectsInvalidStatus(): void
    {
        $host = (new SiteHost())->setHost('example.com')->setSurface(SurfaceType::Site);

        $this->expectException(\InvalidArgumentException::class);
        $host->setStatus('bogus');
    }

    public function testAcceptsKnownStatuses(): void
    {
        $host = (new SiteHost())->setHost('example.com')->setSurface(SurfaceType::Site);
        $host->setStatus('pending');
        $host->setStatus('verified');
        $host->setStatus('active');

        self::assertSame('active', $host->getStatus());
    }
}
