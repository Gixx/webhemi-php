<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\ContentNodeCopier;
use App\Api\ContentNodeInvalidParentException;
use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class ContentNodeCopierTest extends TestCase
{
    public function testCopiesDocumentWithUniqueSlug(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $source = (new ContentNode())
            ->setSite($site)
            ->setTree(ContentTree::Site)
            ->setKind(ContentNodeKind::Document)
            ->setSlug('about')
            ->setTitle('About')
            ->setBody('{"root":{}}');

        $nodes = $this->createMock(ContentNodeRepository::class);
        $nodes->expects(self::exactly(2))
            ->method('findLiveSiblingSlug')
            ->willReturnOnConsecutiveCalls(new ContentNode(), null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist')->with(self::isInstanceOf(ContentNode::class));
        $em->expects(self::once())->method('flush');

        $copy = (new ContentNodeCopier($nodes, $em))->copy($site, $source, null);

        self::assertSame('about-copy', $copy->getSlug());
        self::assertSame('About', $copy->getTitle());
        self::assertSame('{"root":{}}', $copy->getBody());
        self::assertNull($copy->getParent());
    }

    public function testCopiesFolderAndChildren(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $folder = (new ContentNode())
            ->setSite($site)
            ->setTree(ContentTree::Site)
            ->setKind(ContentNodeKind::Folder)
            ->setSlug('docs')
            ->setTitle('Docs');
        $child = (new ContentNode())
            ->setSite($site)
            ->setParent($folder)
            ->setTree(ContentTree::Site)
            ->setKind(ContentNodeKind::Document)
            ->setSlug('intro')
            ->setTitle('Intro')
            ->setBody('x');

        $nodes = $this->createMock(ContentNodeRepository::class);
        $nodes->method('findLiveSiblingSlug')->willReturn(null);
        $nodes->expects(self::once())
            ->method('findLiveChildren')
            ->with($site, ContentTree::Site, $folder)
            ->willReturn([$child]);

        $persisted = [];
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::exactly(2))
            ->method('persist')
            ->willReturnCallback(static function (ContentNode $node) use (&$persisted): void {
                $persisted[] = $node;
            });
        $em->expects(self::once())->method('flush');

        $copy = (new ContentNodeCopier($nodes, $em))->copy($site, $folder, null);

        self::assertSame('docs', $copy->getSlug());
        self::assertCount(2, $persisted);
        self::assertSame($copy, $persisted[0]);
        self::assertSame('intro', $persisted[1]->getSlug());
        self::assertSame($copy, $persisted[1]->getParent());
    }

    public function testRejectsInvalidParent(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $source = (new ContentNode())
            ->setSite($site)
            ->setTree(ContentTree::Site)
            ->setKind(ContentNodeKind::Document)
            ->setSlug('about')
            ->setTitle('About');

        $nodes = $this->createMock(ContentNodeRepository::class);
        $nodes->method('find')->willReturn(null);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');

        $this->expectException(ContentNodeInvalidParentException::class);
        (new ContentNodeCopier($nodes, $em))->copy($site, $source, 99);
    }
}
