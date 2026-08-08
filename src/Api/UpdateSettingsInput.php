<?php

declare(strict_types=1);

namespace App\Api;

use App\Config\AdminAccessMode;

/**
 * Parsed + validated body for PATCH /admin/api/settings.
 */
final class UpdateSettingsInput
{
    /** @param array<string, string> $fieldErrors */
    public function __construct(
        public readonly ?AdminAccessMode $adminAccess,
        public readonly array $fieldErrors = [],
    ) {
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors && $this->adminAccess instanceof AdminAccessMode;
    }

    public static function fromPayload(mixed $payload): self
    {
        if (!\is_array($payload)) {
            return new self(null, ['adminAccess' => 'Request body must be a JSON object.']);
        }

        if (!\array_key_exists('adminAccess', $payload)) {
            return new self(null, ['adminAccess' => 'Admin access mode is required.']);
        }

        $raw = $payload['adminAccess'];
        if (!\is_string($raw) && !\is_int($raw)) {
            return new self(null, ['adminAccess' => 'Admin access must be "path" or "domain".']);
        }

        $mode = AdminAccessMode::tryFrom(strtolower(trim((string) $raw)));
        if (null === $mode) {
            return new self(null, ['adminAccess' => 'Admin access must be "path" or "domain".']);
        }

        return new self($mode);
    }
}
