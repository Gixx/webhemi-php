<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\User;
use App\Repository\ContentNodeRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ContentNodeSoftDeleter
{
    public function __construct(
        private readonly ContentNodeRepository $nodes,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function softDelete(ContentNode $node, ?User $actor): void
    {
        if ($node->isDeleted()) {
            return;
        }

        $now = new \DateTimeImmutable();
        $targets = ContentNodeKind::Folder === $node->getKind()
            ? $this->nodes->findLiveDescendantsInclusive($node)
            : [$node];

        foreach ($targets as $target) {
            if ($target->isDeleted()) {
                continue;
            }
            $target
                ->setOriginalParent($target->getParent())
                ->setDeletedAt($now)
                ->setDeletedBy($actor)
                ->touch();
        }

        $this->em->flush();
    }
}
