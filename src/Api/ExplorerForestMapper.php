<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\MediaAsset;
use App\Entity\PublicationStatus;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use App\Repository\MediaAssetRepository;

/**
 * Builds the four-root File Explorer forest for a site (Slice 2).
 *
 * Shape matches webhemi-ui `ExplorerItem` (string ids, roles, nested children).
 */
final class ExplorerForestMapper
{
    public function __construct(
        private readonly ContentNodeRepository $nodes,
        private readonly MediaAssetRepository $media,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function build(Site $site): array
    {
        $siteId = (int) $site->getId();
        $siteNodes = $this->nodes->findLiveByTree($site, ContentTree::Site);
        $mediaFolders = $this->nodes->findLiveByTree($site, ContentTree::Media);
        $liveAssets = $this->media->findLiveAll($site);
        $trashNodes = $this->nodes->findTrash($site);
        $trashMedia = $this->media->findTrash($site);

        return [
            $this->buildSiteRoot($site, $siteNodes),
            $this->buildMediaRoot($siteId, $mediaFolders, $liveAssets),
            $this->buildTrashRoot($siteId, $trashNodes, $trashMedia),
            $this->buildSettingsRoot($siteId),
        ];
    }

    /**
     * @param list<ContentNode> $nodes
     *
     * @return array<string, mixed>
     */
    private function buildSiteRoot(Site $site, array $nodes): array
    {
        $siteId = (int) $site->getId();

        return [
            'id' => 'site-' . $siteId,
            'label' => $site->getName(),
            'kind' => 'site',
            'role' => 'site',
            'typeLabel' => 'Website',
            'children' => $this->nestNodes($nodes, null),
        ];
    }

    /**
     * @param list<ContentNode> $folders
     * @param list<MediaAsset>  $assets
     *
     * @return array<string, mixed>
     */
    private function buildMediaRoot(int $siteId, array $folders, array $assets): array
    {
        $assetsByFolder = [];
        foreach ($assets as $asset) {
            $fid = $asset->getFolderNode()?->getId() ?? 0;
            $assetsByFolder[$fid][] = $asset;
        }

        return [
            'id' => 'site-' . $siteId . '-media',
            'label' => 'Media library',
            'kind' => 'folder-gallery',
            'role' => 'media-library',
            'typeLabel' => 'Media Library',
            'children' => $this->nestMediaTree($folders, $assetsByFolder, null),
        ];
    }

    /**
     * @param list<ContentNode> $nodes
     * @param list<MediaAsset>  $assets
     *
     * @return array<string, mixed>
     */
    private function buildTrashRoot(int $siteId, array $nodes, array $assets): array
    {
        $children = [];
        foreach ($nodes as $node) {
            $item = $this->nodeToItem($node);
            // Flat trash listing — never expand soft-deleted folders in the tree.
            $item['expandable'] = false;
            unset($item['children']);
            $children[] = $item;
        }
        foreach ($assets as $asset) {
            $children[] = $this->assetToItem($asset);
        }

        return [
            'id' => 'site-' . $siteId . '-trash',
            'label' => 'Recycle Bin',
            'kind' => 'trash',
            'role' => 'trash',
            'typeLabel' => 'Recycle Bin',
            'expandable' => false,
            'children' => $children,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSettingsRoot(int $siteId): array
    {
        return [
            'id' => 'site-' . $siteId . '-settings',
            'label' => 'Settings',
            'kind' => 'settings',
            'role' => 'settings',
            'typeLabel' => 'Settings',
            'expandable' => false,
        ];
    }

    /**
     * @param list<ContentNode> $nodes
     *
     * @return list<array<string, mixed>>
     */
    private function nestNodes(array $nodes, ?int $parentId): array
    {
        $out = [];
        foreach ($nodes as $node) {
            $pid = $node->getParent()?->getId();
            if ($pid !== $parentId) {
                continue;
            }
            $item = $this->nodeToItem($node);
            if (ContentNodeKind::Folder === $node->getKind()) {
                $item['children'] = $this->nestNodes($nodes, (int) $node->getId());
            }
            $out[] = $item;
        }

        return $out;
    }

    /**
     * @param list<ContentNode>            $folders
     * @param array<int, list<MediaAsset>> $assetsByFolder
     *
     * @return list<array<string, mixed>>
     */
    private function nestMediaTree(array $folders, array $assetsByFolder, ?int $parentId): array
    {
        $out = [];
        foreach ($folders as $folder) {
            if (ContentNodeKind::Folder !== $folder->getKind()) {
                continue;
            }
            $pid = $folder->getParent()?->getId();
            if ($pid !== $parentId) {
                continue;
            }
            $id = (int) $folder->getId();
            $item = $this->nodeToItem($folder);
            $childFolders = $this->nestMediaTree($folders, $assetsByFolder, $id);
            $childAssets = [];
            foreach ($assetsByFolder[$id] ?? [] as $asset) {
                $childAssets[] = $this->assetToItem($asset);
            }
            $item['children'] = [...$childFolders, ...$childAssets];
            $out[] = $item;
        }

        $rootKey = $parentId ?? 0;
        if (null === $parentId) {
            foreach ($assetsByFolder[$rootKey] ?? [] as $asset) {
                $out[] = $this->assetToItem($asset);
            }
        }

        return $out;
    }

    /**
     * @return array<string, mixed>
     */
    private function nodeToItem(ContentNode $node): array
    {
        $kind = $node->getKind();
        [$icon, $role, $typeLabel] = match ($kind) {
            ContentNodeKind::Folder => ['folder', 'folder', 'Folder'],
            ContentNodeKind::Document => [
                PublicationStatus::Draft === $node->getPublication() ? 'file-draft' : 'file-document',
                'document',
                'HTML Document',
            ],
            ContentNodeKind::MediaRef => ['file-image', 'document', 'Media reference'],
            ContentNodeKind::Redirect => ['general-app', 'document', 'Redirect'],
        };

        $item = [
            'id' => 'node-' . (int) $node->getId(),
            'label' => $node->getTitle(),
            'kind' => $icon,
            'role' => $role,
            'typeLabel' => $typeLabel,
            'hidden' => $node->isHidden(),
            'modifiedAt' => $this->formatModified($node->getUpdatedAt()),
        ];

        if (ContentNodeKind::Folder === $kind) {
            $item['children'] = [];
        }

        return $item;
    }

    /**
     * @return array<string, mixed>
     */
    private function assetToItem(MediaAsset $asset): array
    {
        $mime = strtolower($asset->getMimeType());
        $icon = 'file-image';
        $typeLabel = 'File';
        if (str_starts_with($mime, 'image/')) {
            $icon = 'file-image';
            $typeLabel = 'Image';
        } elseif (str_starts_with($mime, 'audio/')) {
            $icon = 'file-audio';
            $typeLabel = 'Audio';
        } elseif (str_starts_with($mime, 'video/')) {
            $icon = 'file-video';
            $typeLabel = 'Video';
        }

        return [
            'id' => 'media-' . (int) $asset->getId(),
            'label' => $asset->getOriginalFilename(),
            'kind' => $icon,
            'role' => 'media-asset',
            'typeLabel' => $typeLabel,
            'sizeBytes' => $asset->getByteSize(),
            'modifiedAt' => $this->formatModified($asset->getUpdatedAt()),
        ];
    }

    private function formatModified(\DateTimeImmutable $date): string
    {
        return $date->format('n/j/y g:i A');
    }
}
