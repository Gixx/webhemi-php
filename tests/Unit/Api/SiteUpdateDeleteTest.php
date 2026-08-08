<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\SiteDeleter;
use App\Api\SiteHasHostsException;
use App\Api\SiteProtectedException;
use App\Api\SiteUpdater;
use App\Api\UpdateSiteInput;
use App\Entity\Site;
use App\Entity\SiteHost;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class SiteUpdateDeleteTest extends TestCase
{
    public function testUpdateSiteInputRequiresAField(): void
    {
        $input = UpdateSiteInput::fromPayload([]);
        self::assertFalse($input->isValid());
        self::assertArrayHasKey('_body', $input->fieldErrors);
    }

    public function testUpdaterChangesNameAndSlug(): void
    {
        $site = (new Site())->setName('Old')->setSlug('old')->setIsEnabled(true);
        $input = UpdateSiteInput::fromPayload(['name' => 'New', 'slug' => 'new', 'enabled' => false]);
        self::assertTrue($input->isValid());

        $sites = $this->createMock(SiteRepository::class);
        $sites->expects(self::once())
            ->method('findOneBy')
            ->with(['slug' => 'new'])
            ->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $updated = (new SiteUpdater($sites, $em))->update($site, $input);

        self::assertSame('New', $updated->getName());
        self::assertSame('new', $updated->getSlug());
        self::assertFalse($updated->isEnabled());
    }

    public function testUpdaterRejectsSlugChangeOnProtectedSite(): void
    {
        $site = (new Site())->setName('Main')->setSlug('main')->setIsProtected(true);
        $input = UpdateSiteInput::fromPayload(['slug' => 'not-main']);
        self::assertTrue($input->isValid());

        $sites = $this->createStub(SiteRepository::class);
        $em = $this->createStub(EntityManagerInterface::class);

        $this->expectException(SiteProtectedException::class);
        (new SiteUpdater($sites, $em))->update($site, $input);
    }

    public function testUpdaterRejectsDisableOnProtectedSite(): void
    {
        $site = (new Site())->setName('Main')->setSlug('main')->setIsProtected(true);
        $input = UpdateSiteInput::fromPayload(['enabled' => false]);
        self::assertTrue($input->isValid());

        $sites = $this->createStub(SiteRepository::class);
        $em = $this->createStub(EntityManagerInterface::class);

        $this->expectException(SiteProtectedException::class);
        (new SiteUpdater($sites, $em))->update($site, $input);
    }

    public function testUpdaterAllowsRenameOnProtectedSite(): void
    {
        $site = (new Site())->setName('Main')->setSlug('main')->setIsProtected(true);
        $input = UpdateSiteInput::fromPayload(['name' => 'Main site']);
        self::assertTrue($input->isValid());

        $sites = $this->createStub(SiteRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $updated = (new SiteUpdater($sites, $em))->update($site, $input);
        self::assertSame('Main site', $updated->getName());
        self::assertSame('main', $updated->getSlug());
    }

    public function testDeleterRejectsProtectedSite(): void
    {
        $site = (new Site())->setName('Main')->setSlug('main')->setIsProtected(true);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('remove');

        $this->expectException(SiteProtectedException::class);
        (new SiteDeleter($em))->delete($site);
    }

    public function testDeleterRejectsSiteWithHosts(): void
    {
        $site = (new Site())->setName('Main')->setSlug('main');
        $site->addHost((new SiteHost())->setHost('www.example.test'));

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('remove');

        $this->expectException(SiteHasHostsException::class);
        (new SiteDeleter($em))->delete($site);
    }

    public function testDeleterRemovesEmptySite(): void
    {
        $site = (new Site())->setName('Empty')->setSlug('empty');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('remove')->with($site);
        $em->expects(self::once())->method('flush');

        (new SiteDeleter($em))->delete($site);
    }
}
