<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\ExplorerForestMapper;
use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\MediaAsset;
use App\Entity\PublicationStatus;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use App\Repository\MediaAssetRepository;
use PHPUnit\Framework\TestCase;

final class ExplorerForestMapperTest extends TestCase
{
    public function testBuildFourRootsWithNestedSiteAndMedia(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main Site');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 3);

        $docs = (new ContentNode())
            ->setSite($site)
            ->setTree(ContentTree::Site)
            ->setKind(ContentNodeKind::Folder)
            ->setSlug('docs')
            ->setTitle('Docs')
            ->setPublication(PublicationStatus::Published);
        $docsId = new \ReflectionProperty(ContentNode::class, 'id');
        $docsId->setValue($docs, 10);

        $readme = (new ContentNode())
            ->setSite($site)
            ->setParent($docs)
            ->setTree(ContentTree::Site)
            ->setKind(ContentNodeKind::Document)
            ->setSlug('readme')
            ->setTitle('Readme')
            ->setPublication(PublicationStatus::Draft);
        $readmeId = new \ReflectionProperty(ContentNode::class, 'id');
        $readmeId->setValue($readme, 11);

        $gallery = (new ContentNode())
            ->setSite($site)
            ->setTree(ContentTree::Media)
            ->setKind(ContentNodeKind::Folder)
            ->setSlug('gallery')
            ->setTitle('Gallery')
            ->setPublication(PublicationStatus::Published);
        $galleryId = new \ReflectionProperty(ContentNode::class, 'id');
        $galleryId->setValue($gallery, 20);

        $logo = (new MediaAsset())
            ->setSite($site)
            ->setFolderNode($gallery)
            ->setContentHash(str_repeat('a', 64))
            ->setStorageKey('aa/bb')
            ->setMimeType('image/png')
            ->setByteSize(100)
            ->setOriginalFilename('logo.png');
        $logoId = new \ReflectionProperty(MediaAsset::class, 'id');
        $logoId->setValue($logo, 5);

        $trashed = (new ContentNode())
            ->setSite($site)
            ->setTree(ContentTree::Site)
            ->setKind(ContentNodeKind::Document)
            ->setSlug('old')
            ->setTitle('Old page')
            ->setPublication(PublicationStatus::Published);
        $trashed->setDeletedAt(new \DateTimeImmutable('-1 day'));
        $trashedId = new \ReflectionProperty(ContentNode::class, 'id');
        $trashedId->setValue($trashed, 99);

        $nodes = $this->createMock(ContentNodeRepository::class);
        $nodes->method('findLiveByTree')->willReturnCallback(
            static function (Site $s, ContentTree $tree) use ($docs, $readme, $gallery): array {
                return match ($tree) {
                    ContentTree::Site => [$docs, $readme],
                    ContentTree::Media => [$gallery],
                };
            }
        );
        $nodes->method('findTrash')->willReturn([$trashed]);

        $media = $this->createMock(MediaAssetRepository::class);
        $media->method('findLiveAll')->willReturn([$logo]);
        $media->method('findTrash')->willReturn([]);

        $forest = (new ExplorerForestMapper($nodes, $media))->build($site);

        self::assertCount(4, $forest);
        self::assertSame('site-3', $forest[0]['id']);
        self::assertSame('site', $forest[0]['role']);
        self::assertSame('site-main', $forest[0]['kind']);
        self::assertSame('node-10', $forest[0]['children'][0]['id']);
        self::assertSame('folder', $forest[0]['children'][0]['role']);
        self::assertSame('folder', $forest[0]['children'][0]['kind']);
        self::assertSame('published', $forest[0]['children'][0]['publication']);
        self::assertSame('node-11', $forest[0]['children'][0]['children'][0]['id']);
        self::assertSame('file-draft', $forest[0]['children'][0]['children'][0]['kind']);
        self::assertSame('draft', $forest[0]['children'][0]['children'][0]['publication']);

        self::assertSame('site-3-media', $forest[1]['id']);
        self::assertSame('media-library', $forest[1]['role']);
        self::assertSame('node-20', $forest[1]['children'][0]['id']);
        self::assertSame('media-5', $forest[1]['children'][0]['children'][0]['id']);
        self::assertSame('media-asset', $forest[1]['children'][0]['children'][0]['role']);

        self::assertSame('site-3-trash', $forest[2]['id']);
        self::assertFalse($forest[2]['expandable']);
        self::assertSame('node-99', $forest[2]['children'][0]['id']);
        self::assertFalse($forest[2]['children'][0]['expandable']);

        self::assertSame('site-3-settings', $forest[3]['id']);
        self::assertArrayNotHasKey('disabled', $forest[3]);
    }
}
