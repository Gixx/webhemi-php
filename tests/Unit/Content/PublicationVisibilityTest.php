<?php

declare(strict_types=1);

namespace App\Tests\Unit\Content;

use App\Content\PublicationVisibility;
use App\Entity\ContentNode;
use App\Entity\PublicationStatus;
use PHPUnit\Framework\TestCase;

final class PublicationVisibilityTest extends TestCase
{
    public function testDraftNotReachable(): void
    {
        $node = (new ContentNode())->setPublication(PublicationStatus::Draft);
        self::assertFalse((new PublicationVisibility())->isPubliclyReachable($node));
    }

    public function testPublishedReachableAndListable(): void
    {
        $node = (new ContentNode())
            ->setPublication(PublicationStatus::Published)
            ->setHidden(false);
        $vis = new PublicationVisibility();
        self::assertTrue($vis->isPubliclyReachable($node));
        self::assertTrue($vis->isListable($node));
    }

    public function testHiddenIsReachableButNotListable(): void
    {
        $node = (new ContentNode())
            ->setPublication(PublicationStatus::Published)
            ->setHidden(true);
        $vis = new PublicationVisibility();
        self::assertTrue($vis->isPubliclyReachable($node));
        self::assertFalse($vis->isListable($node));
    }

    public function testScheduledUsesPublishAt(): void
    {
        $now = new \DateTimeImmutable('2026-08-15 12:00:00');
        $future = (new ContentNode())
            ->setPublication(PublicationStatus::Scheduled)
            ->setPublishAt(new \DateTimeImmutable('2026-08-16 00:00:00'));
        $past = (new ContentNode())
            ->setPublication(PublicationStatus::Scheduled)
            ->setPublishAt(new \DateTimeImmutable('2026-08-14 00:00:00'));
        $vis = new PublicationVisibility();
        self::assertFalse($vis->isPubliclyReachable($future, $now));
        self::assertTrue($vis->isPubliclyReachable($past, $now));
    }

    public function testDeletedNotReachable(): void
    {
        $node = (new ContentNode())
            ->setPublication(PublicationStatus::Published)
            ->setDeletedAt(new \DateTimeImmutable());
        self::assertFalse((new PublicationVisibility())->isPubliclyReachable($node));
    }
}
