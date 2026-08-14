<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\ContentNodeApiMapper;
use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\FolderType;
use App\Entity\PublicationStatus;
use App\Entity\Site;
use PHPUnit\Framework\TestCase;

final class ContentNodeApiMapperTest extends TestCase
{
    public function testToArray(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $siteRef = new \ReflectionProperty(Site::class, 'id');
        $siteRef->setValue($site, 3);

        $node = (new ContentNode())
            ->setSite($site)
            ->setTree(ContentTree::Site)
            ->setKind(ContentNodeKind::Folder)
            ->setFolderType(FolderType::Normal)
            ->setSlug('docs')
            ->setTitle('Docs')
            ->setPublication(PublicationStatus::Published)
            ->setHidden(true)
            ->setSortOrder(2);
        $nodeRef = new \ReflectionProperty(ContentNode::class, 'id');
        $nodeRef->setValue($node, 11);

        $data = ContentNodeApiMapper::toArray($node);

        self::assertSame(11, $data['id']);
        self::assertSame(3, $data['siteId']);
        self::assertSame('site', $data['tree']);
        self::assertSame('folder', $data['kind']);
        self::assertSame('normal', $data['folderType']);
        self::assertSame('docs', $data['slug']);
        self::assertSame('published', $data['publication']);
        self::assertTrue($data['hidden']);
        self::assertSame(2, $data['sortOrder']);
    }
}
