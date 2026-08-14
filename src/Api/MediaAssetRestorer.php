<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\MediaAsset;
use Doctrine\ORM\EntityManagerInterface;

final class MediaAssetRestorer
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function restore(MediaAsset $asset): MediaAsset
    {
        if (!$asset->isDeleted()) {
            return $asset;
        }
        $asset->setDeletedAt(null)->setDeletedBy(null)->touch();
        $this->em->flush();

        return $asset;
    }
}
