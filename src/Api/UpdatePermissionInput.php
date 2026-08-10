<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed body for PATCH /admin/api/permissions/{id} (optional fields).
 */
final class UpdatePermissionInput
{
    private function __construct(
        public readonly bool $nameProvided,
        public readonly ?string $name,
        public readonly bool $labelProvided,
        public readonly ?string $label,
        public readonly bool $descriptionProvided,
        public readonly ?string $description,
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
            return new self(false, null, false, null, false, null, [
                '_body' => 'JSON object required.',
            ]);
        }

        $fields = [];

        $nameProvided = \array_key_exists('name', $payload);
        $name = null;
        if ($nameProvided) {
            $name = strtolower(trim((string) $payload['name']));
            if ('' === $name) {
                $fields['name'] = 'Name is required.';
            } elseif (strlen($name) > 128) {
                $fields['name'] = 'Name must be at most 128 characters.';
            } elseif (1 !== preg_match('/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', $name)) {
                $fields['name'] = 'Name must be lowercase letters, digits, dots, underscores, or hyphens.';
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

        if (!$nameProvided && !$labelProvided && !$descriptionProvided) {
            $fields['_body'] = 'At least one of name, label, or description is required.';
        }

        return new self(
            $nameProvided,
            $name,
            $labelProvided,
            $label,
            $descriptionProvided,
            $description,
            $fields,
        );
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
