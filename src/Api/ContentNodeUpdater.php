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

final class ContentNodeUpdater
{
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
     * @throws ContentNodeInvalidBodyException
     */
    public function update(ContentNode $node, UpdateContentNodeInput $input): ContentNode
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('UpdateContentNodeInput must be valid before update().');
        }
        if ($node->isDeleted()) {
            throw new ContentNodeInvalidParentException('Cannot update a deleted node; restore it first.');
        }

        $site = $node->getSite();
        if (!$site instanceof Site) {
            throw new ContentNodeInvalidParentException('Node has no site.');
        }

        $parent = $node->getParent();
        if ($input->parentIdProvided) {
            if (null === $input->parentId) {
                $parent = null;
            } else {
                $parent = $this->nodes->find($input->parentId);
                if (
                    !$parent instanceof ContentNode
                    || $parent->getSite()?->getId() !== $site->getId()
                    || $parent->isDeleted()
                    || ContentNodeKind::Folder !== $parent->getKind()
                    || $parent->getTree() !== $node->getTree()
                    || $parent->getId() === $node->getId()
                ) {
                    throw new ContentNodeInvalidParentException('Parent folder is invalid for this site/tree.');
                }
            }
            $node->setParent($parent);
        }

        $slug = $input->slugProvided ? (string) $input->slug : $node->getSlug();
        if (
            !$parent instanceof \App\Entity\ContentNode
            && ContentTree::Site === $node->getTree()
            && \in_array($slug, self::RESERVED_ROOT_SLUGS, true)
        ) {
            throw new ContentReservedSlugException(sprintf('Slug "%s" is reserved.', $slug));
        }

        if ($input->slugProvided || $input->parentIdProvided) {
            $other = $this->nodes->findLiveSiblingSlug(
                $site,
                $node->getTree(),
                $parent,
                $slug,
                $node->getId(),
            );
            if ($other instanceof ContentNode) {
                throw new ContentNodeSlugTakenException();
            }
            $node->setSlug($slug);
        }

        if ($input->titleProvided && null !== $input->title) {
            $node->setTitle($input->title);
        }
        if ($input->folderTypeProvided) {
            if (ContentNodeKind::Folder !== $node->getKind()) {
                throw new ContentNodeInvalidParentException('Folder type is only allowed for folders.');
            }
            $node->setFolderType(FolderType::from((string) $input->folderType));
        }
        if ($input->bodyProvided) {
            if (
                ContentNodeKind::Document === $node->getKind()
                && !LexicalDocumentBody::isValid($input->body)
            ) {
                throw new ContentNodeInvalidBodyException(
                    'Document body must be Lexical editor JSON (object with root) or empty.',
                );
            }
            $node->setBody($input->body);
        }
        if ($input->redirectTargetProvided) {
            $node->setRedirectTarget($input->redirectTarget);
        }
        if ($input->mediaAssetIdProvided) {
            if (null === $input->mediaAssetId) {
                $node->setMediaAsset(null);
            } else {
                $mediaAsset = $this->media->find($input->mediaAssetId);
                if (
                    !$mediaAsset instanceof MediaAsset
                    || $mediaAsset->getSite()?->getId() !== $site->getId()
                    || $mediaAsset->isDeleted()
                ) {
                    throw new ContentNodeInvalidParentException('Media asset is invalid for this site.');
                }
                $node->setMediaAsset($mediaAsset);
            }
        }
        if ($input->publicationProvided && null !== $input->publication) {
            $node->setPublication(PublicationStatus::from($input->publication));
        }
        if ($input->publishAtProvided) {
            $node->setPublishAt($input->publishAt);
        }
        if ($input->hiddenProvided && null !== $input->hidden) {
            $node->setHidden($input->hidden);
        }
        if ($input->sortOrderProvided && null !== $input->sortOrder) {
            $node->setSortOrder($input->sortOrder);
        }

        $node->touch();
        $this->em->flush();

        return $node;
    }
}
