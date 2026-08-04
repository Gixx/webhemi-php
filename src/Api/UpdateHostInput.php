<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed body for PATCH /admin/api/hosts/{id} (optional fields).
 */
final class UpdateHostInput
{
    private function __construct(
        public readonly ?string $host,
        public readonly bool $siteIdProvided,
        public readonly ?int $siteId,
        public readonly ?string $surface,
        public readonly ?bool $active,
        /** @var array<string, string> */
        public readonly array $fieldErrors,
    ) {
    }

    public static function fromPayload(mixed $payload): self
    {
        if (!\is_array($payload)) {
            return new self(null, false, null, null, null, [
                '_body' => 'JSON object required.',
            ]);
        }

        $fields = [];
        $host = null;
        if (\array_key_exists('host', $payload)) {
            $host = strtolower(trim((string) $payload['host']));
            if ('' === $host) {
                $fields['host'] = 'Hostname is required.';
            } elseif (strlen($host) > 191) {
                $fields['host'] = 'Hostname must be at most 191 characters.';
            } elseif (1 !== preg_match('/^[a-z0-9]([a-z0-9.-]*[a-z0-9])?$/', $host)) {
                $fields['host'] = 'Hostname must be a valid domain name.';
            }
        }

        $siteIdProvided = \array_key_exists('siteId', $payload);
        $siteId = null;
        if ($siteIdProvided) {
            if (null === $payload['siteId'] || '' === $payload['siteId']) {
                $siteId = null;
            } else {
                $parsed = filter_var($payload['siteId'], FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1],
                ]);
                if (false === $parsed) {
                    $fields['siteId'] = 'Site must be a positive integer or null.';
                } else {
                    $siteId = (int) $parsed;
                }
            }
        }

        $surface = null;
        if (\array_key_exists('surface', $payload)) {
            $surface = strtolower(trim((string) $payload['surface']));
            if (!\in_array($surface, ['admin', 'site', 'api'], true)) {
                $fields['surface'] = 'Surface must be admin, site, or api.';
                $surface = null;
            }
        }

        $active = null;
        if (\array_key_exists('active', $payload)) {
            $active = filter_var($payload['active'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            if (null === $active) {
                $fields['active'] = 'Active must be a boolean.';
            }
        }

        return new self($host, $siteIdProvided, $siteId, $surface, $active, $fields);
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
