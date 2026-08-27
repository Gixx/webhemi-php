<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\MediaAssetInvalidFolderException;
use App\Api\MediaAssetUpdater;
use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\MediaAsset;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class MediaAssetUpdaterTest extends TestCase
{
    public function testMovesAssetIntoMediaFolder(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $folder = (new ContentNode())
            ->setSite($site)
            ->setTree(ContentTree::Media)
            ->setKind(ContentNodeKind::Folder)
            ->setSlug('gallery')
            ->setTitle('Gallery');
        $this->setEntityId($folder, 10);

        $asset = (new MediaAsset())
            ->setSite($site)
            ->setContentHash('abc')
            ->setStorageKey('ab/abc')
            ->setMimeType('image/png')
            ->setByteSize(12)
            ->setOriginalFilename('a.png');

        $nodes = $this->createMock(ContentNodeRepository::class);
        $nodes->expects(self::once())->method('find')->with(10)->willReturn($folder);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $updated = (new MediaAssetUpdater($nodes, $em))->updateFolder($site, $asset, 10);

        self::assertSame($folder, $updated->getFolderNode());
    }

    public function testMovesAssetToLibraryRoot(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $folder = (new ContentNode())
            ->setSite($site)
            ->setTree(ContentTree::Media)
            ->setKind(ContentNodeKind::Folder)
            ->setSlug('gallery')
            ->setTitle('Gallery');
        $asset = (new MediaAsset())
            ->setSite($site)
            ->setFolderNode($folder)
            ->setContentHash('abc')
            ->setStorageKey('ab/abc')
            ->setMimeType('image/png')
            ->setByteSize(12)
            ->setOriginalFilename('a.png');

        $nodes = $this->createStub(ContentNodeRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $updated = (new MediaAssetUpdater($nodes, $em))->updateFolder($site, $asset, null);

        self::assertNull($updated->getFolderNode());
    }

    public function testRejectsSiteTreeFolder(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $folder = (new ContentNode())
            ->setSite($site)
            ->setTree(ContentTree::Site)
            ->setKind(ContentNodeKind::Folder)
            ->setSlug('docs')
            ->setTitle('Docs');
        $asset = (new MediaAsset())
            ->setSite($site)
            ->setContentHash('abc')
            ->setStorageKey('ab/abc')
            ->setMimeType('image/png')
            ->setByteSize(12)
            ->setOriginalFilename('a.png');

        $nodes = $this->createMock(ContentNodeRepository::class);
        $nodes->method('find')->willReturn($folder);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $this->expectException(MediaAssetInvalidFolderException::class);
        (new MediaAssetUpdater($nodes, $em))->updateFolder($site, $asset, 5);
    }

    private function setEntityId(object $entity, int $id): void
    {
        $ref = new \ReflectionProperty($entity, 'id');
        $ref->setValue($entity, $id);
    }
}
