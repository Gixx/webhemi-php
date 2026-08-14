<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\ContentNode;
use Doctrine\ORM\EntityManagerInterface;

final class ContentNodePurger
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws ContentNodeInvalidParentException
     */
    public function purge(ContentNode $node): void
    {
        if (!$node->isDeleted()) {
            throw new ContentNodeInvalidParentException('Only soft-deleted nodes can be purged.');
        }

        $this->em->remove($node);
        $this->em->flush();
    }
}
