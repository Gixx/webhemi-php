<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed body for PATCH /admin/api/users/{id} (optional fields; no password).
 */
final class UpdateUserInput
{
    /**
     * @param list<int>|null $roleIds
     * @param list<array{siteId: int, roleId: int}>|null $siteAssignments
     * @param array<string, string> $fieldErrors
     */
    private function __construct(
        public readonly bool $emailProvided,
        public readonly ?string $email,
        public readonly bool $roleIdsProvided,
        public readonly ?array $roleIds,
        public readonly bool $siteAssignmentsProvided,
        public readonly ?array $siteAssignments,
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

        if (\array_key_exists('password', $payload)) {
            $fields['password'] = 'Password cannot be changed in this window.';
        }

        $emailProvided = \array_key_exists('email', $payload);
        $email = null;
        if ($emailProvided) {
            $email = strtolower(trim((string) $payload['email']));
            if ('' === $email) {
                $fields['email'] = 'Email is required.';
            } elseif (strlen($email) > 191) {
                $fields['email'] = 'Email must be at most 191 characters.';
            } elseif (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $fields['email'] = 'Email must be a valid email address.';
            }
        }

        $roleIdsProvided = \array_key_exists('roleIds', $payload);
        $roleIds = null;
        if ($roleIdsProvided) {
            $roleIds = UserPayloadParsers::parseIdList($payload['roleIds']);
            if (null === $roleIds) {
                $fields['roleIds'] = 'roleIds must be an array of positive integers.';
            }
        }

        $siteAssignmentsProvided = \array_key_exists('siteAssignments', $payload);
        $siteAssignments = null;
        if ($siteAssignmentsProvided) {
            $siteAssignments = UserPayloadParsers::parseSiteAssignments($payload['siteAssignments']);
            if (null === $siteAssignments) {
                $fields['siteAssignments'] = 'siteAssignments must be an array of {siteId, roleId}.';
            } elseif (
                \count(array_column($siteAssignments, 'siteId'))
                !== \count(array_unique(array_column($siteAssignments, 'siteId')))
            ) {
                $fields['siteAssignments'] = 'Each site may appear only once in siteAssignments.';
            }
        }

        if (!$emailProvided && !$roleIdsProvided && !$siteAssignmentsProvided) {
            $fields['_body'] = 'At least one of email, roleIds, or siteAssignments is required.';
        }

        return new self(
            $emailProvided,
            $email,
            $roleIdsProvided,
            $roleIds,
            $siteAssignmentsProvided,
            $siteAssignments,
            $fields,
        );
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
