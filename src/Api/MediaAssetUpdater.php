<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\MediaAsset;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use Doctrine\ORM\EntityManagerInterface;

/** Reparent a live media asset inside the media library tree. */
final class MediaAssetUpdater
{
    public function __construct(
        private readonly ContentNodeRepository $nodes,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws MediaAssetInvalidFolderException
     */
    public function updateFolder(Site $site, MediaAsset $asset, ?int $folderNodeId): MediaAsset
    {
        if ($asset->getSite()?->getId() !== $site->getId() || $asset->isDeleted()) {
            throw new MediaAssetInvalidFolderException('Media asset is invalid for this site.');
        }

        $folder = null;
        if (null !== $folderNodeId) {
            $folder = $this->nodes->find($folderNodeId);
            if (
                !$folder instanceof ContentNode
                || $folder->getSite()?->getId() !== $site->getId()
                || $folder->isDeleted()
                || ContentNodeKind::Folder !== $folder->getKind()
                || ContentTree::Media !== $folder->getTree()
            ) {
                throw new MediaAssetInvalidFolderException('Folder must be a live media-tree folder.');
            }
        }

        $asset->setFolderNode($folder)->touch();
        $this->em->flush();

        return $asset;
    }
}
