<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\CreateSiteInput;
use App\Api\SiteCreator;
use App\Api\SiteSlugTakenException;
use App\Entity\Site;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class SiteCreatorTest extends TestCase
{
    public function testCreatesSite(): void
    {
        $input = CreateSiteInput::fromPayload(['name' => 'Blog', 'slug' => 'blog']);
        self::assertTrue($input->isValid());

        $sites = $this->createMock(SiteRepository::class);
        $sites->expects(self::once())
            ->method('findOneBy')
            ->with(['slug' => 'blog'])
            ->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist')->with(self::callback(
            static function (Site $site): bool {
                return 'Blog' === $site->getName()
                    && 'blog' === $site->getSlug()
                    && $site->isEnabled();
            },
        ));
        $em->expects(self::once())->method('flush');

        $site = (new SiteCreator($sites, $em))->create($input);

        self::assertSame('blog', $site->getSlug());
    }

    public function testDuplicateSlugThrows(): void
    {
        $input = CreateSiteInput::fromPayload(['name' => 'Blog', 'slug' => 'blog']);
        $existing = (new Site())->setSlug('blog')->setName('Existing');

        $sites = $this->createStub(SiteRepository::class);
        $sites->method('findOneBy')->willReturn($existing);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');

        $this->expectException(SiteSlugTakenException::class);
        (new SiteCreator($sites, $em))->create($input);
    }
}
