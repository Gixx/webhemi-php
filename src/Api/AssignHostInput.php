<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Parsed body for POST /admin/api/hosts/{id}/assign.
 */
final class AssignHostInput
{
    private function __construct(
        public readonly ?int $siteId,
        /** @var array<string, string> */
        public readonly array $fieldErrors,
    ) {
    }

    public static function fromPayload(mixed $payload): self
    {
        if (!\is_array($payload)) {
            return new self(null, [
                '_body' => 'JSON object required.',
            ]);
        }

        $fields = [];
        $siteId = null;
        if (!\array_key_exists('siteId', $payload) || null === $payload['siteId'] || '' === $payload['siteId']) {
            $fields['siteId'] = 'Site is required.';
        } else {
            $parsed = filter_var($payload['siteId'], FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);
            if (false === $parsed) {
                $fields['siteId'] = 'Site must be a positive integer.';
            } else {
                $siteId = (int) $parsed;
            }
        }

        return new self($siteId, $fields);
    }

    public function isValid(): bool
    {
        return [] === $this->fieldErrors && null !== $this->siteId;
    }
}
