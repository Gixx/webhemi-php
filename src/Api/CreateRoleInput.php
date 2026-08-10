<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed + validated body for POST /admin/api/roles.
 */
final class CreateRoleInput
{
    /**
     * @param list<int> $permissionIds
     * @param array<string, string> $fieldErrors
     */
    private function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $description,
        public readonly array $permissionIds,
        public readonly array $fieldErrors,
    ) {
    }

    /**
     * @param mixed $payload Decoded JSON (object → array expected)
     */
    public static function fromPayload(mixed $payload): self
    {
        if (!\is_array($payload)) {
            return new self('', '', '', [], [
                '_body' => 'JSON object required.',
            ]);
        }

        $name = strtoupper(trim((string) ($payload['name'] ?? '')));
        $label = trim((string) ($payload['label'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));
        $permissionIds = self::parsePermissionIds($payload['permissionIds'] ?? []);

        $fields = [];

        if ('' === $name) {
            $fields['name'] = 'Name is required.';
        } elseif (strlen($name) > 64) {
            $fields['name'] = 'Name must be at most 64 characters.';
        } elseif (1 !== preg_match('/^ROLE_[A-Z0-9]+(?:_[A-Z0-9]+)*$/', $name)) {
            $fields['name'] = 'Name must look like ROLE_CUSTOM_NAME (uppercase letters, digits, underscores).';
        } elseif (\in_array($name, [\App\Entity\Role::ADMIN, \App\Entity\Role::SITE_ADMIN], true)) {
            $fields['name'] = 'System role names are reserved.';
        }

        if ('' === $label) {
            $fields['label'] = 'Label is required.';
        } elseif (mb_strlen($label) > 128) {
            $fields['label'] = 'Label must be at most 128 characters.';
        }

        if (null === $permissionIds) {
            $fields['permissionIds'] = 'permissionIds must be an array of positive integers.';
            $permissionIds = [];
        }

        return new self($name, $label, $description, $permissionIds, $fields);
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }

    /**
     * @return list<int>|null null when invalid shape
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
