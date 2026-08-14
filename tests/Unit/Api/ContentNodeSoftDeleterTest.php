<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\ContentNodeSoftDeleter;
use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class ContentNodeSoftDeleterTest extends TestCase
{
    public function testFolderCascadesToDescendants(): void
    {
        $site = (new Site())->setSlug('main')->setName('Main');
        $folder = (new ContentNode())
            ->setSite($site)
            ->setKind(ContentNodeKind::Folder)
            ->setSlug('docs')
            ->setTitle('Docs');
        $child = (new ContentNode())
            ->setSite($site)
            ->setKind(ContentNodeKind::Document)
            ->setSlug('a')
            ->setTitle('A')
            ->setParent($folder);

        $nodes = $this->createStub(ContentNodeRepository::class);
        $nodes->method('findLiveDescendantsInclusive')->willReturn([$folder, $child]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        (new ContentNodeSoftDeleter($nodes, $em))->softDelete($folder, null);

        self::assertTrue($folder->isDeleted());
        self::assertTrue($child->isDeleted());
        self::assertSame($folder, $child->getOriginalParent());
    }
}
