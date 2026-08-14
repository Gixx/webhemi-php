<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\ContentNode;

final class ContentNodeApiMapper
{
    /**
     * @return array{
     *     id: int,
     *     siteId: int,
     *     parentId: int|null,
     *     tree: string,
     *     kind: string,
     *     folderType: string|null,
     *     slug: string,
     *     title: string,
     *     body: string|null,
     *     redirectTarget: string|null,
     *     mediaAssetId: int|null,
     *     publication: string,
     *     publishAt: string|null,
     *     hidden: bool,
     *     sortOrder: int,
     *     deletedAt: string|null,
     *     originalParentId: int|null,
     *     createdAt: string,
     *     updatedAt: string
     * }
     */
    public static function toArray(ContentNode $node): array
    {
        return [
            'id' => (int) $node->getId(),
            'siteId' => (int) $node->getSite()?->getId(),
            'parentId' => $node->getParent()?->getId(),
            'tree' => $node->getTree()->value,
            'kind' => $node->getKind()->value,
            'folderType' => $node->getFolderType()?->value,
            'slug' => $node->getSlug(),
            'title' => $node->getTitle(),
            'body' => $node->getBody(),
            'redirectTarget' => $node->getRedirectTarget(),
            'mediaAssetId' => $node->getMediaAsset()?->getId(),
            'publication' => $node->getPublication()->value,
            'publishAt' => $node->getPublishAt()?->format(\DateTimeInterface::ATOM),
            'hidden' => $node->isHidden(),
            'sortOrder' => $node->getSortOrder(),
            'deletedAt' => $node->getDeletedAt()?->format(\DateTimeInterface::ATOM),
            'originalParentId' => $node->getOriginalParent()?->getId(),
            'createdAt' => $node->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updatedAt' => $node->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
