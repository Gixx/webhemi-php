<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\ContentNodeKind;
use App\Entity\ContentTree;
use App\Entity\FolderType;
use App\Entity\PublicationStatus;

/**
 * Parsed body for POST /admin/api/sites/{siteId}/nodes.
 */
final class CreateContentNodeInput
{
    private function __construct(
        public readonly string $tree,
        public readonly string $kind,
        public readonly ?int $parentId,
        public readonly string $slug,
        public readonly string $title,
        public readonly ?string $folderType,
        public readonly ?string $body,
        public readonly ?string $redirectTarget,
        public readonly ?int $mediaAssetId,
        public readonly string $publication,
        public readonly ?\DateTimeImmutable $publishAt,
        public readonly bool $hidden,
        public readonly int $sortOrder,
        /** @var array<string, string> */
        public readonly array $fieldErrors,
    ) {
    }

    public static function fromPayload(mixed $payload): self
    {
        if (!\is_array($payload)) {
            return new self(
                ContentTree::Site->value,
                ContentNodeKind::Folder->value,
                null,
                '',
                '',
                null,
                null,
                null,
                null,
                PublicationStatus::Draft->value,
                null,
                false,
                0,
                ['_body' => 'JSON object required.'],
            );
        }

        $fields = [];
        $tree = strtolower(trim((string) ($payload['tree'] ?? ContentTree::Site->value)));
        $kind = strtolower(trim((string) ($payload['kind'] ?? '')));
        $slug = strtolower(trim((string) ($payload['slug'] ?? '')));
        $title = trim((string) ($payload['title'] ?? ''));

        $parentId = null;
        if (\array_key_exists('parentId', $payload) && null !== $payload['parentId'] && '' !== $payload['parentId']) {
            if (!is_numeric($payload['parentId'])) {
                $fields['parentId'] = 'Parent id must be an integer or null.';
            } else {
                $parentId = (int) $payload['parentId'];
            }
        }

        $folderType = null;
        if (
            \array_key_exists('folderType', $payload)
            && null !== $payload['folderType']
            && '' !== $payload['folderType']
        ) {
            $folderType = strtolower(trim((string) $payload['folderType']));
        }

        $body = null;
        if (\array_key_exists('body', $payload)) {
            $body = null === $payload['body'] ? null : (string) $payload['body'];
        }
        $redirectTarget = \array_key_exists('redirectTarget', $payload)
            ? (null === $payload['redirectTarget'] ? null : trim((string) $payload['redirectTarget']))
            : null;

        $mediaAssetId = null;
        if (
            \array_key_exists('mediaAssetId', $payload)
            && null !== $payload['mediaAssetId']
            && '' !== $payload['mediaAssetId']
        ) {
            if (!is_numeric($payload['mediaAssetId'])) {
                $fields['mediaAssetId'] = 'Media asset id must be an integer or null.';
            } else {
                $mediaAssetId = (int) $payload['mediaAssetId'];
            }
        }

        $publication = strtolower(trim((string) ($payload['publication'] ?? PublicationStatus::Draft->value)));
        $publishAt = null;
        if (
            \array_key_exists('publishAt', $payload)
            && null !== $payload['publishAt']
            && '' !== $payload['publishAt']
        ) {
            try {
                $publishAt = new \DateTimeImmutable((string) $payload['publishAt']);
            } catch (\Exception) {
                $fields['publishAt'] = 'Publish at must be a valid date-time.';
            }
        }

        $hidden = \array_key_exists('hidden', $payload)
            ? filter_var($payload['hidden'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE)
            : false;
        if (null === $hidden) {
            $fields['hidden'] = 'Hidden must be a boolean.';
            $hidden = false;
        }

        $sortOrder = 0;
        if (\array_key_exists('sortOrder', $payload)) {
            if (!is_numeric($payload['sortOrder'])) {
                $fields['sortOrder'] = 'Sort order must be an integer.';
            } else {
                $sortOrder = (int) $payload['sortOrder'];
            }
        }

        if (null === ContentTree::tryFrom($tree)) {
            $fields['tree'] = 'Tree must be site or media.';
        }
        if (null === ContentNodeKind::tryFrom($kind)) {
            $fields['kind'] = 'Kind must be folder, document, media_ref, or redirect.';
        }
        if ('' === $slug) {
            $fields['slug'] = 'Slug is required.';
        } elseif (strlen($slug) > 128) {
            $fields['slug'] = 'Slug must be at most 128 characters.';
        } elseif (1 !== preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            $fields['slug'] = 'Slug must be lowercase letters, digits, and hyphens.';
        }
        if ('' === $title) {
            $fields['title'] = 'Title is required.';
        } elseif (mb_strlen($title) > 255) {
            $fields['title'] = 'Title must be at most 255 characters.';
        }

        if (ContentNodeKind::Folder->value === $kind) {
            $folderType ??= FolderType::Normal->value;
            if (null === FolderType::tryFrom($folderType)) {
                $fields['folderType'] = 'Folder type must be normal or locale.';
            }
        } elseif (null !== $folderType) {
            $fields['folderType'] = 'Folder type is only allowed for folders.';
        }

        if (ContentNodeKind::Document->value === $kind && null === $body) {
            $body = '';
        }
        if (ContentNodeKind::Redirect->value === $kind && (null === $redirectTarget || '' === $redirectTarget)) {
            $fields['redirectTarget'] = 'Redirect target is required for redirects.';
        }
        if (ContentNodeKind::MediaRef->value === $kind && null === $mediaAssetId) {
            $fields['mediaAssetId'] = 'Media asset id is required for media_ref.';
        }

        if (null === PublicationStatus::tryFrom($publication)) {
            $fields['publication'] = 'Publication must be draft, published, or scheduled.';
        }
        if (PublicationStatus::Scheduled->value === $publication && !$publishAt instanceof \DateTimeImmutable) {
            $fields['publishAt'] = 'Publish at is required when publication is scheduled.';
        }

        return new self(
            $tree,
            $kind,
            $parentId,
            $slug,
            $title,
            $folderType,
            $body,
            $redirectTarget,
            $mediaAssetId,
            $publication,
            $publishAt,
            $hidden,
            $sortOrder,
            $fields,
        );
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
