<?php

declare(strict_types=1);

namespace App\Tests\Unit\SiteHost;

use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use PHPUnit\Framework\TestCase;

final class SiteHostVerificationTest extends TestCase
{
    public function testRejectsInvalidVerification(): void
    {
        $host = (new SiteHost())->setHost('example.com')->setSurface(SurfaceType::Site);

        $this->expectException(\InvalidArgumentException::class);
        $host->setVerification('bogus');
    }

    public function testRejectsLegacyActiveVerification(): void
    {
        $host = (new SiteHost())->setHost('example.com')->setSurface(SurfaceType::Site);

        $this->expectException(\InvalidArgumentException::class);
        $host->setVerification('active');
    }

    public function testAcceptsPendingAndVerified(): void
    {
        $host = (new SiteHost())->setHost('example.com')->setSurface(SurfaceType::Site);
        $host->setVerification('pending');
        $host->setVerification('verified');

        self::assertSame('verified', $host->getVerification());
    }
}
