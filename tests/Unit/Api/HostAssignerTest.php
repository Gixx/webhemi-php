<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\HostAlreadyAssignedException;
use App\Api\HostAssigner;
use App\Api\HostNotVerifiedForAssignException;
use App\Api\HostSiteNotFoundException;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class HostAssignerTest extends TestCase
{
    public function testAssignSetsSiteAndKeepsVerified(): void
    {
        $host = (new SiteHost())->setHost('ok.example.test')->setVerification('verified');
        $site = (new Site())->setName('Main')->setSlug('main');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 3);

        $sites = $this->createMock(SiteRepository::class);
        $sites->expects(self::once())->method('find')->with(3)->willReturn($site);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $updated = (new HostAssigner($sites, $em))->assign($host, 3);

        self::assertSame($site, $updated->getSite());
        self::assertSame('verified', $updated->getVerification());
    }

    public function testAssignRejectsPending(): void
    {
        $host = (new SiteHost())->setHost('pending.example.test')->setVerification('pending');
        $sites = $this->createStub(SiteRepository::class);
        $em = $this->createStub(EntityManagerInterface::class);

        $this->expectException(HostNotVerifiedForAssignException::class);
        (new HostAssigner($sites, $em))->assign($host, 1);
    }

    public function testAssignRejectsAlreadyBound(): void
    {
        $site = (new Site())->setName('Bound')->setSlug('bound');
        $host = (new SiteHost())
            ->setHost('bound.example.test')
            ->setVerification('verified')
            ->setSite($site);

        $sites = $this->createStub(SiteRepository::class);
        $em = $this->createStub(EntityManagerInterface::class);

        $this->expectException(HostAlreadyAssignedException::class);
        (new HostAssigner($sites, $em))->assign($host, 2);
    }

    public function testAssignRejectsMissingSite(): void
    {
        $host = (new SiteHost())->setHost('ok.example.test')->setVerification('verified');
        $sites = $this->createMock(SiteRepository::class);
        $sites->expects(self::once())->method('find')->with(99)->willReturn(null);
        $em = $this->createStub(EntityManagerInterface::class);

        $this->expectException(HostSiteNotFoundException::class);
        (new HostAssigner($sites, $em))->assign($host, 99);
    }
}
