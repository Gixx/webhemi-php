<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed + validated body for POST /admin/api/sites.
 */
final class CreateSiteInput
{
    private function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly bool $enabled,
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
            return new self('', '', true, [
                '_body' => 'JSON object required.',
            ]);
        }

        $name = trim((string) ($payload['name'] ?? ''));
        $slugRaw = trim((string) ($payload['slug'] ?? ''));
        $slug = strtolower($slugRaw);
        $enabled = \array_key_exists('enabled', $payload)
            ? filter_var($payload['enabled'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE)
            : true;

        $fields = [];

        if ('' === $name) {
            $fields['name'] = 'Name is required.';
        } elseif (mb_strlen($name) > 128) {
            $fields['name'] = 'Name must be at most 128 characters.';
        }

        if ('' === $slug) {
            $fields['slug'] = 'Slug is required.';
        } elseif (strlen($slug) > 64) {
            $fields['slug'] = 'Slug must be at most 64 characters.';
        } elseif (1 !== preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            $fields['slug'] = 'Slug must be lowercase letters, digits, and hyphens.';
        }

        if (null === $enabled) {
            $fields['enabled'] = 'Enabled must be a boolean.';
            $enabled = true;
        }

        return new self($name, $slug, $enabled, $fields);
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
