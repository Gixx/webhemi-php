<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\MediaAsset;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class MediaAssetSoftDeleter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function softDelete(MediaAsset $asset, ?User $actor): void
    {
        if ($asset->isDeleted()) {
            return;
        }
        $asset
            ->setDeletedAt(new \DateTimeImmutable())
            ->setDeletedBy($actor)
            ->touch();
        $this->em->flush();
    }
}
