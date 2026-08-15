<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed body for PATCH /admin/api/sites/{siteId}/settings.
 */
final class UpdateSiteSettingsInput
{
    private function __construct(
        public readonly bool $nameProvided,
        public readonly ?string $name,
        public readonly bool $descriptionProvided,
        public readonly ?string $description,
        public readonly bool $faviconMediaIdProvided,
        public readonly ?int $faviconMediaId,
        /** @var array<string, string> */
        public readonly array $fieldErrors,
    ) {
    }

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
            } elseif (\strlen($name) > 128) {
                $fields['name'] = 'Name must be at most 128 characters.';
            }
        }

        $descriptionProvided = \array_key_exists('description', $payload);
        $description = null;
        if ($descriptionProvided) {
            $description = null === $payload['description'] ? null : (string) $payload['description'];
        }

        $faviconProvided = \array_key_exists('faviconMediaId', $payload);
        $faviconMediaId = null;
        if ($faviconProvided) {
            if (null === $payload['faviconMediaId'] || '' === $payload['faviconMediaId']) {
                $faviconMediaId = null;
            } elseif (!is_numeric($payload['faviconMediaId'])) {
                $fields['faviconMediaId'] = 'Favicon media id must be an integer or null.';
            } else {
                $faviconMediaId = (int) $payload['faviconMediaId'];
            }
        }

        return new self(
            $nameProvided,
            $name,
            $descriptionProvided,
            $description,
            $faviconProvided,
            $faviconMediaId,
            $fields,
        );
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors
            && ($this->nameProvided || $this->descriptionProvided || $this->faviconMediaIdProvided);
    }
}
