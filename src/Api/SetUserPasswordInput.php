<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed + validated body for POST /admin/api/users/{id}/password.
 *
 * Self: currentPassword required. Admin reset of another user: new password only.
 */
final class SetUserPasswordInput
{
    /**
     * @param array<string, string> $fieldErrors
     */
    private function __construct(
        public readonly string $currentPassword,
        public readonly string $password,
        public readonly bool $requireCurrentPassword,
        public readonly array $fieldErrors,
    ) {
    }

    /**
     * @param mixed $payload Decoded JSON (object → array expected)
     */
    public static function fromPayload(mixed $payload, bool $requireCurrentPassword = true): self
    {
        if (!\is_array($payload)) {
            return new self('', '', $requireCurrentPassword, [
                '_body' => 'JSON object required.',
            ]);
        }

        $currentPassword = (string) ($payload['currentPassword'] ?? '');
        $password = (string) ($payload['password'] ?? '');
        $confirm = \array_key_exists('confirmPassword', $payload)
            ? (string) $payload['confirmPassword']
            : null;

        $fields = [];

        if ($requireCurrentPassword && '' === $currentPassword) {
            $fields['currentPassword'] = 'Current password is required.';
        }

        if ('' === $password) {
            $fields['password'] = 'Password is required.';
        } elseif (strlen($password) < 8) {
            $fields['password'] = 'Password must be at least 8 characters.';
        } elseif (strlen($password) > 4096) {
            $fields['password'] = 'Password is too long.';
        }

        if (null !== $confirm && $confirm !== $password) {
            $fields['confirmPassword'] = 'Passwords do not match.';
        }

        return new self($currentPassword, $password, $requireCurrentPassword, $fields);
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
