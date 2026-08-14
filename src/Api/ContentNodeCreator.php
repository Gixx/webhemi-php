<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\FolderType;
use App\Entity\MediaAsset;
use App\Entity\PublicationStatus;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use App\Repository\MediaAssetRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ContentNodeCreator
{
    /** Root-level site-tree slugs that collide with reserved public paths. */
    private const RESERVED_ROOT_SLUGS = ['admin', 'api', 'login', 'register'];

    public function __construct(
        private readonly ContentNodeRepository $nodes,
        private readonly MediaAssetRepository $media,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws ContentNodeSlugTakenException
     * @throws ContentNodeInvalidParentException
     * @throws ContentReservedSlugException
     */
    public function create(Site $site, CreateContentNodeInput $input): ContentNode
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('CreateContentNodeInput must be valid before create().');
        }

        $tree = ContentTree::from($input->tree);
        $kind = ContentNodeKind::from($input->kind);
        $parent = null;
        if (null !== $input->parentId) {
            $parent = $this->nodes->find($input->parentId);
            if (
                !$parent instanceof ContentNode
                || $parent->getSite()?->getId() !== $site->getId()
                || $parent->isDeleted()
                || ContentNodeKind::Folder !== $parent->getKind()
                || $parent->getTree() !== $tree
            ) {
                throw new ContentNodeInvalidParentException('Parent folder is invalid for this site/tree.');
            }
        }

        if (
            !$parent instanceof \App\Entity\ContentNode
            && ContentTree::Site === $tree
            && \in_array($input->slug, self::RESERVED_ROOT_SLUGS, true)
        ) {
            throw new ContentReservedSlugException(sprintf('Slug "%s" is reserved.', $input->slug));
        }

        if ($this->nodes->findLiveSiblingSlug($site, $tree, $parent, $input->slug) instanceof ContentNode) {
            throw new ContentNodeSlugTakenException();
        }

        $mediaAsset = null;
        if (null !== $input->mediaAssetId) {
            $mediaAsset = $this->media->find($input->mediaAssetId);
            if (
                !$mediaAsset instanceof MediaAsset
                || $mediaAsset->getSite()?->getId() !== $site->getId()
                || $mediaAsset->isDeleted()
            ) {
                throw new ContentNodeInvalidParentException('Media asset is invalid for this site.');
            }
        }

        $node = (new ContentNode())
            ->setSite($site)
            ->setParent($parent)
            ->setTree($tree)
            ->setKind($kind)
            ->setSlug($input->slug)
            ->setTitle($input->title)
            ->setBody($input->body)
            ->setRedirectTarget($input->redirectTarget)
            ->setMediaAsset($mediaAsset)
            ->setPublication(PublicationStatus::from($input->publication))
            ->setPublishAt($input->publishAt)
            ->setHidden($input->hidden)
            ->setSortOrder($input->sortOrder);

        if (ContentNodeKind::Folder === $kind) {
            $node->setFolderType(FolderType::from((string) $input->folderType));
        }

        $this->em->persist($node);
        $this->em->flush();

        return $node;
    }
}
