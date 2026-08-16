<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed body for PATCH /admin/api/users/{id}.
 * Optional password: omit or empty string = leave unchanged; non-empty must be ≥8 chars.
 */
final class UpdateUserInput
{
    /**
     * @param list<int>|null                            $roleIds
     * @param list<array{siteId: int, roleId: int}>|null $siteAssignments
     * @param array<string, string>                      $fieldErrors
     */
    private function __construct(
        public readonly bool $emailProvided,
        public readonly ?string $email,
        public readonly bool $passwordProvided,
        public readonly ?string $password,
        public readonly bool $displayNameProvided,
        public readonly ?string $displayName,
        public readonly bool $telephoneProvided,
        public readonly ?string $telephone,
        public readonly bool $addressProvided,
        public readonly ?string $address,
        public readonly bool $zipProvided,
        public readonly ?string $zip,
        public readonly bool $cityProvided,
        public readonly ?string $city,
        public readonly bool $countryProvided,
        public readonly ?string $country,
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
            return new self(
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                false,
                null,
                ['_body' => 'JSON object required.'],
            );
        }

        $fields = [];

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

        $passwordProvided = false;
        $password = null;
        if (\array_key_exists('password', $payload)) {
            $raw = (string) $payload['password'];
            if ('' !== $raw) {
                $passwordProvided = true;
                $password = $raw;
                if (strlen($password) < 8) {
                    $fields['password'] = 'Password must be at least 8 characters.';
                } elseif (strlen($password) > 4096) {
                    $fields['password'] = 'Password is too long.';
                }
            }
        }

        $stringFields = [
            'displayName' => 128,
            'telephone' => 64,
            'address' => 255,
            'zip' => 32,
            'city' => 128,
            'country' => 128,
        ];
        $parsedStrings = [];
        foreach ($stringFields as $key => $max) {
            $provided = \array_key_exists($key, $payload);
            $value = null;
            if ($provided) {
                if (null === $payload[$key] || '' === $payload[$key]) {
                    if ('displayName' === $key) {
                        $fields['displayName'] = 'Name is required.';
                    }
                    $value = null;
                } else {
                    $value = trim((string) $payload[$key]);
                    if ('' === $value && 'displayName' === $key) {
                        $fields['displayName'] = 'Name is required.';
                    } elseif (strlen($value) > $max) {
                        $fields[$key] = sprintf('%s is too long.', ucfirst($key));
                    }
                }
            }
            $parsedStrings[$key] = [$provided, $value];
        }

        $roleIdsProvided = \array_key_exists('roleIds', $payload);
        $roleIds = null;
        if ($roleIdsProvided) {
            $roleIds = UserPayloadParsers::parseIdList($payload['roleIds']);
            if (null === $roleIds) {
                $fields['roleIds'] = 'roleIds must be an array of positive integers.';
            } elseif ([] === $roleIds) {
                $fields['roleIds'] = 'At least one role is required.';
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

        $hasAny =
            $emailProvided
            || $passwordProvided
            || $parsedStrings['displayName'][0]
            || $parsedStrings['telephone'][0]
            || $parsedStrings['address'][0]
            || $parsedStrings['zip'][0]
            || $parsedStrings['city'][0]
            || $parsedStrings['country'][0]
            || $roleIdsProvided
            || $siteAssignmentsProvided;

        if (!$hasAny) {
            $fields['_body'] = 'At least one updatable field is required.';
        }

        return new self(
            $emailProvided,
            $email,
            $passwordProvided,
            $password,
            $parsedStrings['displayName'][0],
            $parsedStrings['displayName'][1],
            $parsedStrings['telephone'][0],
            $parsedStrings['telephone'][1],
            $parsedStrings['address'][0],
            $parsedStrings['address'][1],
            $parsedStrings['zip'][0],
            $parsedStrings['zip'][1],
            $parsedStrings['city'][0],
            $parsedStrings['city'][1],
            $parsedStrings['country'][0],
            $parsedStrings['country'][1],
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
