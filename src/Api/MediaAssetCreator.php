<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\MediaAsset;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use App\Repository\MediaAssetRepository;
use Doctrine\ORM\EntityManagerInterface;

final class MediaAssetCreator
{
    public function __construct(
        private readonly MediaAssetRepository $media,
        private readonly ContentNodeRepository $nodes,
        private readonly MediaBlobStore $blobs,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws MediaAssetInvalidFolderException
     *
     * @return array{asset: MediaAsset, deduped: bool}
     */
    public function createFromUpload(
        Site $site,
        string $absolutePath,
        string $originalFilename,
        ?string $mimeType,
        ?int $folderNodeId,
    ): array {
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

        $stored = $this->blobs->storeFromPath($absolutePath, $originalFilename, $mimeType);
        $existing = $this->media->findBySiteAndHash($site, $stored['contentHash']);
        if ($existing instanceof MediaAsset) {
            if ($existing->isDeleted()) {
                $existing
                    ->setDeletedAt(null)
                    ->setDeletedBy(null)
                    ->setFolderNode($folder)
                    ->setOriginalFilename($stored['originalFilename'])
                    ->setMimeType($stored['mimeType'])
                    ->setByteSize($stored['byteSize'])
                    ->touch();
                $this->em->flush();

                return ['asset' => $existing, 'deduped' => true];
            }

            return ['asset' => $existing, 'deduped' => true];
        }

        $asset = (new MediaAsset())
            ->setSite($site)
            ->setFolderNode($folder)
            ->setContentHash($stored['contentHash'])
            ->setStorageKey($stored['storageKey'])
            ->setMimeType($stored['mimeType'])
            ->setByteSize($stored['byteSize'])
            ->setOriginalFilename($stored['originalFilename']);

        $this->em->persist($asset);
        $this->em->flush();

        return ['asset' => $asset, 'deduped' => false];
    }
}
