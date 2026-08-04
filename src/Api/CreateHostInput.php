<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed + validated body for POST /admin/api/hosts.
 * `siteId` is optional (null = unassigned host).
 */
final class CreateHostInput
{
    private function __construct(
        public readonly string $host,
        public readonly ?int $siteId,
        public readonly string $surface,
        public readonly bool $active,
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
            return new self('', null, 'site', true, [
                '_body' => 'JSON object required.',
            ]);
        }

        $host = strtolower(trim((string) ($payload['host'] ?? '')));
        $siteId = null;
        if (\array_key_exists('siteId', $payload) && null !== $payload['siteId'] && '' !== $payload['siteId']) {
            $parsed = filter_var($payload['siteId'], FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);
            $siteId = false === $parsed ? false : (int) $parsed;
        }
        $surfaceRaw = strtolower(trim((string) ($payload['surface'] ?? 'site')));
        $active = \array_key_exists('active', $payload)
            ? filter_var($payload['active'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE)
            : true;

        $fields = [];

        if ('' === $host) {
            $fields['host'] = 'Hostname is required.';
        } elseif (strlen($host) > 191) {
            $fields['host'] = 'Hostname must be at most 191 characters.';
        } elseif (1 !== preg_match('/^[a-z0-9]([a-z0-9.-]*[a-z0-9])?$/', $host)) {
            $fields['host'] = 'Hostname must be a valid domain name.';
        }

        if (false === $siteId) {
            $fields['siteId'] = 'Site must be a positive integer or omitted.';
            $siteId = null;
        }

        if (!\in_array($surfaceRaw, ['admin', 'site', 'api'], true)) {
            $fields['surface'] = 'Surface must be admin, site, or api.';
            $surfaceRaw = 'site';
        }

        if (null === $active) {
            $fields['active'] = 'Active must be a boolean.';
            $active = true;
        }

        return new self($host, $siteId, $surfaceRaw, $active, $fields);
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
