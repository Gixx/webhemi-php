<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Deep-copy a live content node (and folder descendants) under a target parent.
 */
final class ContentNodeCopier
{
    private const RESERVED_ROOT_SLUGS = ['admin', 'api', 'login', 'register'];

    public function __construct(
        private readonly ContentNodeRepository $nodes,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws ContentNodeInvalidParentException
     * @throws ContentReservedSlugException
     * @throws ContentNodeInvalidParentException when source is deleted / wrong site
     */
    public function copy(Site $site, ContentNode $source, ?int $parentId): ContentNode
    {
        if ($source->getSite()?->getId() !== $site->getId() || $source->isDeleted()) {
            throw new ContentNodeInvalidParentException('Source node is invalid for this site.');
        }

        $tree = $source->getTree();
        $parent = $this->resolveParent($site, $tree, $parentId);
        $slug = $this->allocateUniqueSlug($site, $tree, $parent, $source->getSlug());

        if (
            !$parent instanceof ContentNode
            && ContentTree::Site === $tree
            && \in_array($slug, self::RESERVED_ROOT_SLUGS, true)
        ) {
            throw new ContentReservedSlugException(sprintf('Slug "%s" is reserved.', $slug));
        }

        $rootCopy = $this->cloneNode($site, $source, $parent, $slug);
        $this->em->persist($rootCopy);

        if (ContentNodeKind::Folder === $source->getKind()) {
            $this->copyChildren($site, $source, $rootCopy);
        }

        $this->em->flush();

        return $rootCopy;
    }

    private function resolveParent(Site $site, ContentTree $tree, ?int $parentId): ?ContentNode
    {
        if (null === $parentId) {
            return null;
        }

        $parent = $this->nodes->find($parentId);
        if (
            !$parent instanceof ContentNode
            || $parent->getSite()?->getId() !== $site->getId()
            || $parent->isDeleted()
            || ContentNodeKind::Folder !== $parent->getKind()
            || $parent->getTree() !== $tree
        ) {
            throw new ContentNodeInvalidParentException('Parent folder is invalid for this site/tree.');
        }

        return $parent;
    }

    private function allocateUniqueSlug(
        Site $site,
        ContentTree $tree,
        ?ContentNode $parent,
        string $desired,
    ): string {
        $desired = strtolower(trim($desired));
        if ('' === $desired) {
            $desired = 'copy';
        }

        if (!$this->nodes->findLiveSiblingSlug($site, $tree, $parent, $desired) instanceof ContentNode) {
            return $this->truncateSlug($desired);
        }

        $base = $this->truncateSlug($desired, 120);
        $candidate = $this->truncateSlug($base . '-copy');
        $n = 1;
        while ($this->nodes->findLiveSiblingSlug($site, $tree, $parent, $candidate) instanceof ContentNode) {
            ++$n;
            $candidate = $this->truncateSlug($base . '-copy-' . $n);
        }

        return $candidate;
    }

    private function truncateSlug(string $slug, int $max = 128): string
    {
        if (strlen($slug) <= $max) {
            return $slug;
        }

        return substr($slug, 0, $max);
    }

    private function cloneNode(
        Site $site,
        ContentNode $source,
        ?ContentNode $parent,
        string $slug,
    ): ContentNode {
        $copy = (new ContentNode())
            ->setSite($site)
            ->setParent($parent)
            ->setTree($source->getTree())
            ->setKind($source->getKind())
            ->setSlug($slug)
            ->setTitle($source->getTitle())
            ->setBody($source->getBody())
            ->setRedirectTarget($source->getRedirectTarget())
            ->setMediaAsset($source->getMediaAsset())
            ->setPublication($source->getPublication())
            ->setPublishAt($source->getPublishAt())
            ->setHidden($source->isHidden())
            ->setSortOrder($source->getSortOrder());

        if (ContentNodeKind::Folder === $source->getKind()) {
            $copy->setFolderType($source->getFolderType());
        }

        return $copy;
    }

    private function copyChildren(Site $site, ContentNode $sourceFolder, ContentNode $targetFolder): void
    {
        $children = $this->nodes->findLiveChildren($site, $sourceFolder->getTree(), $sourceFolder);
        foreach ($children as $child) {
            // Under a newly created unique folder, keep original child slugs.
            $childCopy = $this->cloneNode($site, $child, $targetFolder, $child->getSlug());
            $this->em->persist($childCopy);
            if (ContentNodeKind::Folder === $child->getKind()) {
                $this->copyChildren($site, $child, $childCopy);
            }
        }
    }
}
