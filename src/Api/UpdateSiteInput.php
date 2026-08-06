<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed body for PATCH /admin/api/sites/{id} (optional fields).
 */
final class UpdateSiteInput
{
    private function __construct(
        public readonly bool $nameProvided,
        public readonly ?string $name,
        public readonly bool $slugProvided,
        public readonly ?string $slug,
        public readonly bool $enabledProvided,
        public readonly ?bool $enabled,
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
            $name = trim((string) $payload['name']);
            if ('' === $name) {
                $fields['name'] = 'Name is required.';
            } elseif (mb_strlen($name) > 128) {
                $fields['name'] = 'Name must be at most 128 characters.';
            }
        }

        $slugProvided = \array_key_exists('slug', $payload);
        $slug = null;
        if ($slugProvided) {
            $slug = strtolower(trim((string) $payload['slug']));
            if ('' === $slug) {
                $fields['slug'] = 'Slug is required.';
            } elseif (strlen($slug) > 64) {
                $fields['slug'] = 'Slug must be at most 64 characters.';
            } elseif (1 !== preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
                $fields['slug'] = 'Slug must be lowercase letters, digits, and hyphens.';
            }
        }

        $enabledProvided = \array_key_exists('enabled', $payload);
        $enabled = null;
        if ($enabledProvided) {
            $enabled = filter_var($payload['enabled'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            if (null === $enabled) {
                $fields['enabled'] = 'Enabled must be a boolean.';
            }
        }

        if (!$nameProvided && !$slugProvided && !$enabledProvided) {
            $fields['_body'] = 'At least one of name, slug, or enabled is required.';
        }

        return new self($nameProvided, $name, $slugProvided, $slug, $enabledProvided, $enabled, $fields);
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
