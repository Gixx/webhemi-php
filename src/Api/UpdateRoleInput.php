<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed body for PATCH /admin/api/roles/{id} (optional fields).
 */
final class UpdateRoleInput
{
    /**
     * @param list<int>|null $permissionIds
     * @param array<string, string> $fieldErrors
     */
    private function __construct(
        public readonly bool $nameProvided,
        public readonly ?string $name,
        public readonly bool $labelProvided,
        public readonly ?string $label,
        public readonly bool $descriptionProvided,
        public readonly ?string $description,
        public readonly bool $permissionIdsProvided,
        public readonly ?array $permissionIds,
        public readonly array $fieldErrors,
    ) {
    }

    /**
     * @param mixed $payload Decoded JSON (object → array expected)
     */
    public static function fromPayload(mixed $payload): self
    {
        if (!\is_array($payload)) {
            return new self(false, null, false, null, false, null, false, null, [
                '_body' => 'JSON object required.',
            ]);
        }

        $fields = [];

        $nameProvided = \array_key_exists('name', $payload);
        $name = null;
        if ($nameProvided) {
            $name = strtoupper(trim((string) $payload['name']));
            if ('' === $name) {
                $fields['name'] = 'Name is required.';
            } elseif (strlen($name) > 64) {
                $fields['name'] = 'Name must be at most 64 characters.';
            } elseif (1 !== preg_match('/^ROLE_[A-Z0-9]+(?:_[A-Z0-9]+)*$/', $name)) {
                $fields['name'] = 'Name must look like ROLE_CUSTOM_NAME (uppercase letters, digits, underscores).';
            } elseif (\in_array($name, [\App\Entity\Role::ADMIN, \App\Entity\Role::SITE_ADMIN], true)) {
                $fields['name'] = 'System role names are reserved.';
            }
        }

        $labelProvided = \array_key_exists('label', $payload);
        $label = null;
        if ($labelProvided) {
            $label = trim((string) $payload['label']);
            if ('' === $label) {
                $fields['label'] = 'Label is required.';
            } elseif (mb_strlen($label) > 128) {
                $fields['label'] = 'Label must be at most 128 characters.';
            }
        }

        $descriptionProvided = \array_key_exists('description', $payload);
        $description = null;
        if ($descriptionProvided) {
            $description = trim((string) $payload['description']);
        }

        $permissionIdsProvided = \array_key_exists('permissionIds', $payload);
        $permissionIds = null;
        if ($permissionIdsProvided) {
            $permissionIds = self::parsePermissionIds($payload['permissionIds']);
            if (null === $permissionIds) {
                $fields['permissionIds'] = 'permissionIds must be an array of positive integers.';
            }
        }

        if (!$nameProvided && !$labelProvided && !$descriptionProvided && !$permissionIdsProvided) {
            $fields['_body'] = 'At least one of name, label, description, or permissionIds is required.';
        }

        return new self(
            $nameProvided,
            $name,
            $labelProvided,
            $label,
            $descriptionProvided,
            $description,
            $permissionIdsProvided,
            $permissionIds,
            $fields,
        );
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }

    /**
     * @return list<int>|null
     */
    private static function parsePermissionIds(mixed $raw): ?array
    {
        if (!\is_array($raw)) {
            return null;
        }
        $ids = [];
        foreach ($raw as $value) {
            if (\is_int($value) && $value > 0) {
                $ids[] = $value;
                continue;
            }
            if (\is_string($value) && ctype_digit($value) && (int) $value > 0) {
                $ids[] = (int) $value;
                continue;
            }

            return null;
        }

        return array_values(array_unique($ids));
    }
}
