<?php

declare(strict_types=1);

namespace App\Api;

use App\Config\AdminAccessMode;

/**
 * Parsed + validated body for PATCH /admin/api/settings.
 */
final class UpdateSettingsInput
{
    /**
     * @param array<string, string> $fieldErrors
     */
    public function __construct(
        public readonly ?AdminAccessMode $adminAccess,
        public readonly ?bool $symfonyDebugToolbar = null,
        public readonly array $fieldErrors = [],
    ) {
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors
            && ($this->adminAccess instanceof AdminAccessMode || null !== $this->symfonyDebugToolbar);
    }

    public static function fromPayload(mixed $payload): self
    {
        if (!\is_array($payload)) {
            return new self(null, null, ['_' => 'Request body must be a JSON object.']);
        }

        $hasAccess = \array_key_exists('adminAccess', $payload);
        $hasToolbar = \array_key_exists('symfonyDebugToolbar', $payload);

        if (!$hasAccess && !$hasToolbar) {
            return new self(null, null, [
                '_' => 'At least one of adminAccess or symfonyDebugToolbar is required.',
            ]);
        }

        $adminAccess = null;
        $fieldErrors = [];

        if ($hasAccess) {
            $raw = $payload['adminAccess'];
            if (!\is_string($raw) && !\is_int($raw)) {
                $fieldErrors['adminAccess'] = 'Admin access must be "path" or "domain".';
            } else {
                $mode = AdminAccessMode::tryFrom(strtolower(trim((string) $raw)));
                if (null === $mode) {
                    $fieldErrors['adminAccess'] = 'Admin access must be "path" or "domain".';
                } else {
                    $adminAccess = $mode;
                }
            }
        }

        $toolbar = null;
        if ($hasToolbar) {
            $rawToolbar = $payload['symfonyDebugToolbar'];
            if (!\is_bool($rawToolbar)) {
                $fieldErrors['symfonyDebugToolbar'] = 'Debug toolbar must be a boolean.';
            } else {
                $toolbar = $rawToolbar;
            }
        }

        return new self($adminAccess, $toolbar, $fieldErrors);
    }
}
