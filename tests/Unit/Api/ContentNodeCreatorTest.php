<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\ContentNodeCreator;
use App\Api\ContentNodeSlugTakenException;
use App\Api\ContentReservedSlugException;
use App\Api\CreateContentNodeInput;
use App\Entity\ContentNode;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use App\Repository\MediaAssetRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class ContentNodeCreatorTest extends TestCase
{
    public function testCreatesFolder(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $input = CreateContentNodeInput::fromPayload([
            'tree' => 'site',
            'kind' => 'folder',
            'slug' => 'docs',
            'title' => 'Docs',
        ]);
        self::assertTrue($input->isValid());

        $nodes = $this->createMock(ContentNodeRepository::class);
        $nodes->expects(self::once())->method('findLiveSiblingSlug')->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist')->with(self::isInstanceOf(ContentNode::class));
        $em->expects(self::once())->method('flush');

        $media = $this->createStub(MediaAssetRepository::class);
        $node = (new ContentNodeCreator($nodes, $media, $em))->create($site, $input);

        self::assertSame('docs', $node->getSlug());
        self::assertSame('Docs', $node->getTitle());
        self::assertFalse($node->isHidden());
    }

    public function testRejectsReservedRootSlug(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $input = CreateContentNodeInput::fromPayload([
            'kind' => 'folder',
            'slug' => 'admin',
            'title' => 'Admin',
        ]);

        $nodes = $this->createStub(ContentNodeRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');
        $media = $this->createStub(MediaAssetRepository::class);

        $this->expectException(ContentReservedSlugException::class);
        (new ContentNodeCreator($nodes, $media, $em))->create($site, $input);
    }

    public function testDuplicateSlugThrows(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $input = CreateContentNodeInput::fromPayload([
            'kind' => 'document',
            'slug' => 'hello',
            'title' => 'Hello',
            'body' => 'x',
        ]);

        $nodes = $this->createStub(ContentNodeRepository::class);
        $nodes->method('findLiveSiblingSlug')->willReturn(new ContentNode());
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');
        $media = $this->createStub(MediaAssetRepository::class);

        $this->expectException(ContentNodeSlugTakenException::class);
        (new ContentNodeCreator($nodes, $media, $em))->create($site, $input);
    }
}
