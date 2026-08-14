<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\FolderType;
use App\Entity\PublicationStatus;

/**
 * Parsed body for PATCH /admin/api/sites/{siteId}/nodes/{id}.
 */
final class UpdateContentNodeInput
{
    private function __construct(
        public readonly bool $parentIdProvided,
        public readonly ?int $parentId,
        public readonly bool $slugProvided,
        public readonly ?string $slug,
        public readonly bool $titleProvided,
        public readonly ?string $title,
        public readonly bool $folderTypeProvided,
        public readonly ?string $folderType,
        public readonly bool $bodyProvided,
        public readonly ?string $body,
        public readonly bool $redirectTargetProvided,
        public readonly ?string $redirectTarget,
        public readonly bool $mediaAssetIdProvided,
        public readonly ?int $mediaAssetId,
        public readonly bool $publicationProvided,
        public readonly ?string $publication,
        public readonly bool $publishAtProvided,
        public readonly ?\DateTimeImmutable $publishAt,
        public readonly bool $hiddenProvided,
        public readonly ?bool $hidden,
        public readonly bool $sortOrderProvided,
        public readonly ?int $sortOrder,
        /** @var array<string, string> */
        public readonly array $fieldErrors,
    ) {
    }

    public static function fromPayload(mixed $payload): self
    {
        if (!\is_array($payload)) {
            return new self(
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                ['_body' => 'JSON object required.'],
            );
        }

        $fields = [];
        $any = false;

        $parentIdProvided = \array_key_exists('parentId', $payload);
        $parentId = null;
        if ($parentIdProvided) {
            $any = true;
            if (null === $payload['parentId'] || '' === $payload['parentId']) {
                $parentId = null;
            } elseif (!is_numeric($payload['parentId'])) {
                $fields['parentId'] = 'Parent id must be an integer or null.';
            } else {
                $parentId = (int) $payload['parentId'];
            }
        }

        $slugProvided = \array_key_exists('slug', $payload);
        $slug = null;
        if ($slugProvided) {
            $any = true;
            $slug = strtolower(trim((string) $payload['slug']));
            if ('' === $slug) {
                $fields['slug'] = 'Slug is required.';
            } elseif (strlen($slug) > 128) {
                $fields['slug'] = 'Slug must be at most 128 characters.';
            } elseif (1 !== preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
                $fields['slug'] = 'Slug must be lowercase letters, digits, and hyphens.';
            }
        }

        $titleProvided = \array_key_exists('title', $payload);
        $title = null;
        if ($titleProvided) {
            $any = true;
            $title = trim((string) $payload['title']);
            if ('' === $title) {
                $fields['title'] = 'Title is required.';
            } elseif (mb_strlen($title) > 255) {
                $fields['title'] = 'Title must be at most 255 characters.';
            }
        }

        $folderTypeProvided = \array_key_exists('folderType', $payload);
        $folderType = null;
        if ($folderTypeProvided) {
            $any = true;
            if (null === $payload['folderType'] || '' === $payload['folderType']) {
                $fields['folderType'] = 'Folder type cannot be empty.';
            } else {
                $folderType = strtolower(trim((string) $payload['folderType']));
                if (null === FolderType::tryFrom($folderType)) {
                    $fields['folderType'] = 'Folder type must be normal or locale.';
                }
            }
        }

        $bodyProvided = \array_key_exists('body', $payload);
        $body = null;
        if ($bodyProvided) {
            $any = true;
            $body = null === $payload['body'] ? null : (string) $payload['body'];
        }

        $redirectTargetProvided = \array_key_exists('redirectTarget', $payload);
        $redirectTarget = null;
        if ($redirectTargetProvided) {
            $any = true;
            $redirectTarget = null === $payload['redirectTarget']
                ? null
                : trim((string) $payload['redirectTarget']);
        }

        $mediaAssetIdProvided = \array_key_exists('mediaAssetId', $payload);
        $mediaAssetId = null;
        if ($mediaAssetIdProvided) {
            $any = true;
            if (null === $payload['mediaAssetId'] || '' === $payload['mediaAssetId']) {
                $mediaAssetId = null;
            } elseif (!is_numeric($payload['mediaAssetId'])) {
                $fields['mediaAssetId'] = 'Media asset id must be an integer or null.';
            } else {
                $mediaAssetId = (int) $payload['mediaAssetId'];
            }
        }

        $publicationProvided = \array_key_exists('publication', $payload);
        $publication = null;
        if ($publicationProvided) {
            $any = true;
            $publication = strtolower(trim((string) $payload['publication']));
            if (null === PublicationStatus::tryFrom($publication)) {
                $fields['publication'] = 'Publication must be draft, published, or scheduled.';
            }
        }

        $publishAtProvided = \array_key_exists('publishAt', $payload);
        $publishAt = null;
        if ($publishAtProvided) {
            $any = true;
            if (null === $payload['publishAt'] || '' === $payload['publishAt']) {
                $publishAt = null;
            } else {
                try {
                    $publishAt = new \DateTimeImmutable((string) $payload['publishAt']);
                } catch (\Exception) {
                    $fields['publishAt'] = 'Publish at must be a valid date-time.';
                }
            }
        }

        $hiddenProvided = \array_key_exists('hidden', $payload);
        $hidden = null;
        if ($hiddenProvided) {
            $any = true;
            $hidden = filter_var($payload['hidden'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            if (null === $hidden) {
                $fields['hidden'] = 'Hidden must be a boolean.';
            }
        }

        $sortOrderProvided = \array_key_exists('sortOrder', $payload);
        $sortOrder = null;
        if ($sortOrderProvided) {
            $any = true;
            if (!is_numeric($payload['sortOrder'])) {
                $fields['sortOrder'] = 'Sort order must be an integer.';
            } else {
                $sortOrder = (int) $payload['sortOrder'];
            }
        }

        if (!$any) {
            $fields['_body'] = 'At least one field is required.';
        }

        return new self(
            $parentIdProvided,
            $parentId,
            $slugProvided,
            $slug,
            $titleProvided,
            $title,
            $folderTypeProvided,
            $folderType,
            $bodyProvided,
            $body,
            $redirectTargetProvided,
            $redirectTarget,
            $mediaAssetIdProvided,
            $mediaAssetId,
            $publicationProvided,
            $publication,
            $publishAtProvided,
            $publishAt,
            $hiddenProvided,
            $hidden,
            $sortOrderProvided,
            $sortOrder,
            $fields,
        );
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
