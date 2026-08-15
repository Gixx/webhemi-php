<?php

declare(strict_types=1);

namespace App\Content;

use App\Entity\ContentNode;
use App\Entity\PublicationStatus;

/**
 * Public reachability for CMS nodes (request-time scheduled publish).
 */
final class PublicationVisibility
{
    public function isPubliclyReachable(ContentNode $node, ?\DateTimeImmutable $now = null): bool
    {
        if ($node->isDeleted()) {
            return false;
        }

        $now ??= new \DateTimeImmutable();

        return match ($node->getPublication()) {
            PublicationStatus::Published => true,
            PublicationStatus::Scheduled => $node->getPublishAt() instanceof \DateTimeImmutable
                && $node->getPublishAt() <= $now,
            PublicationStatus::Draft => false,
        };
    }

    /**
     * Reachable and eligible for folder indexes / nav (hidden = unlisted).
     */
    public function isListable(ContentNode $node, ?\DateTimeImmutable $now = null): bool
    {
        return $this->isPubliclyReachable($node, $now) && !$node->isHidden();
    }
}
