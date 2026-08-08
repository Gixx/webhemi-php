<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\HostAdminSurfaceNotAllowedException;
use App\Api\HostAlreadyAssignedException;
use App\Api\HostAssigner;
use App\Api\HostUnassigner;
use App\Api\HostUpdater;
use App\Api\UpdateHostInput;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class HostUpdaterTest extends TestCase
{
    public function testSameSiteIdDoesNotReassign(): void
    {
        $site = (new Site())->setName('Main')->setSlug('main');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 2);

        $host = (new SiteHost())
            ->setHost('www.example.test')
            ->setSite($site)
            ->setVerification('verified')
            ->setIsEnabled(true);
        $hostRef = new \ReflectionProperty(SiteHost::class, 'id');
        $hostRef->setValue($host, 9);

        $hosts = $this->createStub(SiteHostRepository::class);
        $sites = $this->createStub(SiteRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $input = UpdateHostInput::fromPayload([
            'host' => 'www.example.test',
            'siteId' => 2,
            'surface' => 'site',
            'enabled' => true,
        ]);
        self::assertTrue($input->isValid());

        $updated = (new HostUpdater(
            $em,
            $hosts,
            new HostUnassigner($em),
            new HostAssigner($sites, $em),
        ))->update($host, $input);

        self::assertSame('verified', $updated->getVerification());
        self::assertSame($site, $updated->getSite());
        self::assertTrue($updated->isEnabled());
    }

    public function testRejectsAdminSurfaceWhileAssignedToNonMain(): void
    {
        $site = (new Site())->setName('Blog')->setSlug('blog');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 5);

        $host = (new SiteHost())
            ->setHost('www.blog.test')
            ->setSite($site)
            ->setSurface(SurfaceType::Site)
            ->setVerification('verified');

        $hosts = $this->createStub(SiteHostRepository::class);
        $sites = $this->createStub(SiteRepository::class);
        $em = $this->createStub(EntityManagerInterface::class);

        $input = UpdateHostInput::fromPayload(['surface' => 'admin']);
        self::assertTrue($input->isValid());

        $this->expectException(HostAdminSurfaceNotAllowedException::class);
        (new HostUpdater(
            $em,
            $hosts,
            new HostUnassigner($em),
            new HostAssigner($sites, $em),
        ))->update($host, $input);
    }

    public function testDifferentSiteWhenAssignedThrows(): void
    {
        $site = (new Site())->setName('Main')->setSlug('main');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 2);

        $host = (new SiteHost())
            ->setHost('www.example.test')
            ->setSite($site)
            ->setVerification('verified');

        $hosts = $this->createStub(SiteHostRepository::class);
        $sites = $this->createStub(SiteRepository::class);
        $em = $this->createStub(EntityManagerInterface::class);

        $input = UpdateHostInput::fromPayload(['siteId' => 5]);
        self::assertTrue($input->isValid());

        $this->expectException(HostAlreadyAssignedException::class);
        (new HostUpdater(
            $em,
            $hosts,
            new HostUnassigner($em),
            new HostAssigner($sites, $em),
        ))->update($host, $input);
    }
}
