<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ContentNodeRestorer
{
    public function __construct(
        private readonly ContentNodeRepository $nodes,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws ContentNodeRestoreConflictException
     * @throws ContentNodeInvalidParentException
     */
    public function restore(ContentNode $node): ContentNode
    {
        if (!$node->isDeleted()) {
            return $node;
        }

        $site = $node->getSite();
        if (!$site instanceof Site) {
            throw new ContentNodeInvalidParentException('Node has no site.');
        }

        $parent = $node->getOriginalParent() ?? $node->getParent();
        if ($parent instanceof ContentNode) {
            $invalidParent = $parent->isDeleted()
                || $parent->getSite()?->getId() !== $site->getId()
                || ContentNodeKind::Folder !== $parent->getKind()
                || $parent->getTree() !== $node->getTree();
            if ($invalidParent) {
                throw new ContentNodeInvalidParentException(
                    'Cannot restore: original parent is missing or deleted.',
                );
            }
        }

        $conflict = $this->nodes->findLiveSiblingSlug(
            $site,
            $node->getTree(),
            $parent,
            $node->getSlug(),
            $node->getId(),
        );
        if ($conflict instanceof ContentNode) {
            throw new ContentNodeRestoreConflictException(
                'Cannot restore: a live node with the same slug exists under the target parent.',
            );
        }

        $node
            ->setParent($parent)
            ->setOriginalParent(null)
            ->setDeletedAt(null)
            ->setDeletedBy(null)
            ->touch();

        $this->em->flush();

        return $node;
    }
}
