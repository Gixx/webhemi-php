<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed + validated body for POST /admin/api/permissions.
 */
final class CreatePermissionInput
{
    private function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $description,
        /** @var array<string, string> */
        public readonly array $fieldErrors,
    ) {
    }

    /**
     * @param mixed $payload Decoded JSON (object → array expected)
     */
    public static function fromPayload(mixed $payload): self
    {
        if (!\is_array($payload)) {
            return new self('', '', '', [
                '_body' => 'JSON object required.',
            ]);
        }

        $name = strtolower(trim((string) ($payload['name'] ?? '')));
        $label = trim((string) ($payload['label'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));

        $fields = [];

        if ('' === $name) {
            $fields['name'] = 'Name is required.';
        } elseif (strlen($name) > 128) {
            $fields['name'] = 'Name must be at most 128 characters.';
        } elseif (1 !== preg_match('/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', $name)) {
            $fields['name'] = 'Name must be lowercase letters, digits, dots, underscores, or hyphens.';
        }

        if ('' === $label) {
            $fields['label'] = 'Label is required.';
        } elseif (mb_strlen($label) > 128) {
            $fields['label'] = 'Label must be at most 128 characters.';
        }

        return new self($name, $label, $description, $fields);
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
