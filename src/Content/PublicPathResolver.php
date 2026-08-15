<?php

declare(strict_types=1);

namespace App\Content;

use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\Site;
use App\Repository\ContentNodeRepository;
use App\Routing\ReservedPaths;

/**
 * Resolves a public request path to a live site-tree node (or site-root index).
 */
final class PublicPathResolver
{
    public function __construct(
        private readonly ContentNodeRepository $nodes,
        private readonly PublicationVisibility $visibility,
        private readonly ReservedPaths $reservedPaths,
    ) {
    }

    public function resolve(Site $site, string $path, ?\DateTimeImmutable $now = null): ?PublicContentHit
    {
        $path = $this->normalizePath($path);
        if (
            $this->reservedPaths->isReservedOnSiteHost($path)
            || $this->reservedPaths->isAdminPath($path)
        ) {
            return null;
        }

        if ('/' === $path) {
            return PublicContentHit::rootIndex();
        }

        $trailingSlash = str_ends_with($path, '/');
        $trimmed = trim($path, '/');
        if ('' === $trimmed) {
            return PublicContentHit::rootIndex();
        }

        $segments = explode('/', $trimmed);
        $last = $segments[array_key_last($segments)];
        $isHtmlLeaf = str_ends_with($last, '.html');

        if ($isHtmlLeaf) {
            if ($trailingSlash || strlen($last) <= 5) {
                return null;
            }
            $leafSlug = substr($last, 0, -5);
            if ('' === $leafSlug || str_contains($leafSlug, '.')) {
                return null;
            }
            $folderSegments = \array_slice($segments, 0, -1);
        } else {
            if (!$trailingSlash) {
                // Folders require a trailing slash; do not guess.
                return null;
            }
            $leafSlug = null;
            $folderSegments = $segments;
        }

        $parent = null;
        $folderPath = '/';
        foreach ($folderSegments as $slug) {
            if ('' === $slug || str_contains($slug, '.')) {
                return null;
            }
            $folder = $this->nodes->findLiveSiblingSlug(
                $site,
                ContentTree::Site,
                $parent,
                $slug,
            );
            if (
                !$folder instanceof ContentNode
                || ContentNodeKind::Folder !== $folder->getKind()
                || !$this->visibility->isPubliclyReachable($folder, $now)
            ) {
                return null;
            }
            $parent = $folder;
            $folderPath .= $slug . '/';
        }

        if (null === $leafSlug) {
            if (!$parent instanceof ContentNode) {
                return PublicContentHit::rootIndex();
            }

            return PublicContentHit::folder($parent, $folderPath);
        }

        $leaf = $this->nodes->findLiveSiblingSlug(
            $site,
            ContentTree::Site,
            $parent,
            $leafSlug,
        );
        if (
            !$leaf instanceof ContentNode
            || ContentNodeKind::Folder === $leaf->getKind()
            || !$this->visibility->isPubliclyReachable($leaf, $now)
        ) {
            return null;
        }

        $leafPath = $folderPath . $leafSlug . '.html';

        return PublicContentHit::leaf($leaf, $leafPath);
    }

    /**
     * @return list<ContentNode>
     */
    public function listableChildren(Site $site, ?ContentNode $parent, ?\DateTimeImmutable $now = null): array
    {
        $children = $this->nodes->findLiveChildren($site, ContentTree::Site, $parent);
        $out = [];
        foreach ($children as $child) {
            if ($this->visibility->isListable($child, $now)) {
                $out[] = $child;
            }
        }

        return $out;
    }

    public function childHref(string $folderPath, ContentNode $child): string
    {
        $base = '/' === $folderPath ? '/' : (str_ends_with($folderPath, '/') ? $folderPath : $folderPath . '/');
        if (ContentNodeKind::Folder === $child->getKind()) {
            return $base . $child->getSlug() . '/';
        }

        return $base . $child->getSlug() . '.html';
    }

    private function normalizePath(string $path): string
    {
        if ('' === $path || !str_starts_with($path, '/')) {
            $path = '/' . $path;
        }
        // Keep a single trailing slash meaning for folders; collapse duplicate slashes.
        $path = preg_replace('#/+#', '/', $path) ?? $path;

        return $path;
    }
}
