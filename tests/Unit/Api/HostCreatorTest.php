<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\CreateHostInput;
use App\Api\HostCreator;
use App\Api\HostHostTakenException;
use App\Api\HostSiteNotFoundException;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class HostCreatorTest extends TestCase
{
    public function testCreatesHostWithSite(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'www.example.test',
            'siteId' => 2,
            'surface' => 'site',
        ]);
        self::assertTrue($input->isValid());

        $site = (new Site())->setName('Main')->setSlug('main');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 2);

        $sites = $this->createMock(SiteRepository::class);
        $sites->expects(self::once())->method('find')->with(2)->willReturn($site);

        $hosts = $this->createMock(SiteHostRepository::class);
        $hosts->expects(self::once())
            ->method('findOneBy')
            ->with(['host' => 'www.example.test'])
            ->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist')->with(self::callback(
            static function (SiteHost $host): bool {
                return 'www.example.test' === $host->getHost()
                    && 'pending' === $host->getStatus()
                    && $host->isActive()
                    && 'site' === $host->getSurface()->value
                    && null !== $host->getSite();
            },
        ));
        $em->expects(self::once())->method('flush');

        $created = (new HostCreator($sites, $hosts, $em))->create($input);

        self::assertSame('www.example.test', $created->getHost());
        self::assertSame($site, $created->getSite());
    }

    public function testCreatesHostWithoutSite(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'orphan.example.test',
        ]);
        self::assertTrue($input->isValid());

        $sites = $this->createMock(SiteRepository::class);
        $sites->expects(self::never())->method('find');

        $hosts = $this->createStub(SiteHostRepository::class);
        $hosts->method('findOneBy')->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist')->with(self::callback(
            static function (SiteHost $host): bool {
                return null === $host->getSite();
            },
        ));
        $em->expects(self::once())->method('flush');

        $created = (new HostCreator($sites, $hosts, $em))->create($input);

        self::assertNull($created->getSite());
    }

    public function testDuplicateHostThrows(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'www.example.test',
            'siteId' => 1,
        ]);

        $site = (new Site())->setName('Main')->setSlug('main');
        $sites = $this->createStub(SiteRepository::class);
        $sites->method('find')->willReturn($site);

        $existing = (new SiteHost())->setHost('www.example.test')->setSite($site);
        $hosts = $this->createStub(SiteHostRepository::class);
        $hosts->method('findOneBy')->willReturn($existing);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');

        $this->expectException(HostHostTakenException::class);
        (new HostCreator($sites, $hosts, $em))->create($input);
    }

    public function testMissingSiteThrows(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'www.example.test',
            'siteId' => 99,
        ]);

        $sites = $this->createStub(SiteRepository::class);
        $sites->method('find')->willReturn(null);

        $hosts = $this->createMock(SiteHostRepository::class);
        $hosts->expects(self::never())->method('findOneBy');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');

        $this->expectException(HostSiteNotFoundException::class);
        (new HostCreator($sites, $hosts, $em))->create($input);
    }
}
