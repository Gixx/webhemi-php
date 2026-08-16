<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed + validated body for POST /admin/api/users.
 */
final class CreateUserInput
{
    /**
     * @param list<int>                                 $roleIds
     * @param list<array{siteId: int, roleId: int}>      $siteAssignments
     * @param array<string, string>                      $fieldErrors
     */
    private function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $displayName,
        public readonly ?string $telephone,
        public readonly ?string $address,
        public readonly ?string $zip,
        public readonly ?string $city,
        public readonly ?string $country,
        public readonly array $roleIds,
        public readonly array $siteAssignments,
        public readonly array $fieldErrors,
    ) {
    }

    /**
     * @param mixed $payload Decoded JSON (object → array expected)
     */
    public static function fromPayload(mixed $payload): self
    {
        if (!\is_array($payload)) {
            return new self('', '', '', null, null, null, null, null, [], [], [
                '_body' => 'JSON object required.',
            ]);
        }

        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $password = (string) ($payload['password'] ?? '');
        $displayName = trim((string) ($payload['displayName'] ?? ''));
        $roleIds = UserPayloadParsers::parseIdList($payload['roleIds'] ?? []);
        $siteAssignments = UserPayloadParsers::parseSiteAssignments($payload['siteAssignments'] ?? []);

        $fields = [];

        if ('' === $displayName) {
            $fields['displayName'] = 'Name is required.';
        } elseif (strlen($displayName) > 128) {
            $fields['displayName'] = 'Name must be at most 128 characters.';
        }

        if ('' === $email) {
            $fields['email'] = 'Email is required.';
        } elseif (strlen($email) > 191) {
            $fields['email'] = 'Email must be at most 191 characters.';
        } elseif (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fields['email'] = 'Email must be a valid email address.';
        }

        if ('' === $password) {
            $fields['password'] = 'Password is required.';
        } elseif (strlen($password) < 8) {
            $fields['password'] = 'Password must be at least 8 characters.';
        } elseif (strlen($password) > 4096) {
            $fields['password'] = 'Password is too long.';
        }

        $profile = self::parseProfileFields($payload, $fields);

        if (null === $roleIds) {
            $fields['roleIds'] = 'roleIds must be an array of positive integers.';
            $roleIds = [];
        }

        if (null === $siteAssignments) {
            $fields['siteAssignments'] = 'siteAssignments must be an array of {siteId, roleId}.';
            $siteAssignments = [];
        } else {
            $siteIds = array_column($siteAssignments, 'siteId');
            if (\count($siteIds) !== \count(array_unique($siteIds))) {
                $fields['siteAssignments'] = 'Each site may appear only once in siteAssignments.';
            }
        }

        return new self(
            $email,
            $password,
            $displayName,
            $profile['telephone'],
            $profile['address'],
            $profile['zip'],
            $profile['city'],
            $profile['country'],
            $roleIds,
            $siteAssignments,
            $fields,
        );
    }

    /**
     * @param array<string, mixed>  $payload
     * @param array<string, string> $fields
     *
     * @return array{
     *     telephone: ?string,
     *     address: ?string,
     *     zip: ?string,
     *     city: ?string,
     *     country: ?string
     * }
     */
    public static function parseProfileFields(array $payload, array &$fields): array
    {
        $limits = [
            'telephone' => 64,
            'address' => 255,
            'zip' => 32,
            'city' => 128,
            'country' => 128,
        ];
        $out = [];
        foreach ($limits as $key => $max) {
            if (!\array_key_exists($key, $payload) || null === $payload[$key] || '' === $payload[$key]) {
                $out[$key] = null;
                continue;
            }
            $value = trim((string) $payload[$key]);
            if (strlen($value) > $max) {
                $fields[$key] = sprintf('%s is too long.', ucfirst($key));
            }
            $out[$key] = '' === $value ? null : $value;
        }

        return $out;
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
