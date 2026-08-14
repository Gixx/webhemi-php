<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\MediaAsset;
use App\Repository\ContentNodeRepository;
use Doctrine\ORM\EntityManagerInterface;

final class MediaAssetPurger
{
    public function __construct(
        private readonly ContentNodeRepository $nodes,
        private readonly MediaBlobStore $blobs,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws MediaAssetInUseException
     * @throws ContentNodeInvalidParentException
     */
    public function purge(MediaAsset $asset): void
    {
        if (!$asset->isDeleted()) {
            throw new ContentNodeInvalidParentException('Only soft-deleted media can be purged.');
        }

        $id = (int) $asset->getId();
        if ($this->nodes->countLiveMediaRefs($id) > 0) {
            throw new MediaAssetInUseException('Media asset is still referenced by live media_ref nodes.');
        }

        $storageKey = $asset->getStorageKey();
        $this->em->remove($asset);
        $this->em->flush();
        $this->blobs->deleteIfExists($storageKey);
    }
}
