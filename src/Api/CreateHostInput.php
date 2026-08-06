<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed + validated body for POST /admin/api/hosts.
 * Hosts are always created unassigned; assign after verify.
 */
final class CreateHostInput
{
    private function __construct(
        public readonly string $host,
        public readonly ?int $siteId,
        public readonly string $surface,
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
            return new self('', null, 'site', true, [
                '_body' => 'JSON object required.',
            ]);
        }

        $host = strtolower(trim((string) ($payload['host'] ?? '')));
        $siteId = null;
        $fields = [];

        if (\array_key_exists('siteId', $payload) && null !== $payload['siteId'] && '' !== $payload['siteId']) {
            $fields['siteId'] = 'Create the host without a site; assign after ownership is verified.';
        }

        $surfaceRaw = strtolower(trim((string) ($payload['surface'] ?? 'site')));

        $enabledRaw = $payload['enabled'] ?? $payload['active'] ?? true;
        $enabled = filter_var($enabledRaw, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        if ('' === $host) {
            $fields['host'] = 'Hostname is required.';
        } elseif (strlen($host) > 191) {
            $fields['host'] = 'Hostname must be at most 191 characters.';
        } elseif (1 !== preg_match('/^[a-z0-9]([a-z0-9.-]*[a-z0-9])?$/', $host)) {
            $fields['host'] = 'Hostname must be a valid domain name.';
        }

        if (!\in_array($surfaceRaw, ['admin', 'site', 'api'], true)) {
            $fields['surface'] = 'Surface must be admin, site, or api.';
            $surfaceRaw = 'site';
        }

        if (null === $enabled) {
            $fields['enabled'] = 'Enabled must be a boolean.';
            $enabled = true;
        }

        return new self($host, $siteId, $surfaceRaw, $enabled, $fields);
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors;
    }
}
