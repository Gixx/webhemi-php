<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\MediaAsset;

final class MediaAssetApiMapper
{
    /**
     * @return array{
     *     id: int,
     *     siteId: int,
     *     folderNodeId: int|null,
     *     contentHash: string,
     *     storageKey: string,
     *     mimeType: string,
     *     byteSize: int,
     *     originalFilename: string,
     *     deletedAt: string|null,
     *     createdAt: string,
     *     updatedAt: string
     * }
     */
    public static function toArray(MediaAsset $asset): array
    {
        return [
            'id' => (int) $asset->getId(),
            'siteId' => (int) $asset->getSite()?->getId(),
            'folderNodeId' => $asset->getFolderNode()?->getId(),
            'contentHash' => $asset->getContentHash(),
            'storageKey' => $asset->getStorageKey(),
            'mimeType' => $asset->getMimeType(),
            'byteSize' => $asset->getByteSize(),
            'originalFilename' => $asset->getOriginalFilename(),
            'deletedAt' => $asset->getDeletedAt()?->format(\DateTimeInterface::ATOM),
            'createdAt' => $asset->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updatedAt' => $asset->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
