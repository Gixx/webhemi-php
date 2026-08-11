<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Shared JSON payload parsers for user create/update bodies.
 */
final class UserPayloadParsers
{
    /**
     * @return list<int>|null
     */
    public static function parseIdList(mixed $raw): ?array
    {
        if (!\is_array($raw)) {
            return null;
        }
        $ids = [];
        foreach ($raw as $value) {
            if (\is_int($value) && $value > 0) {
                $ids[] = $value;
                continue;
            }
            if (\is_string($value) && ctype_digit($value) && (int) $value > 0) {
                $ids[] = (int) $value;
                continue;
            }

            return null;
        }

        return array_values(array_unique($ids));
    }

    /**
     * @return list<array{siteId: int, roleId: int}>|null
     */
    public static function parseSiteAssignments(mixed $raw): ?array
    {
        if (!\is_array($raw)) {
            return null;
        }
        $out = [];
        foreach ($raw as $row) {
            if (!\is_array($row)) {
                return null;
            }
            $siteId = $row['siteId'] ?? null;
            $roleId = $row['roleId'] ?? null;
            if (\is_string($siteId) && ctype_digit($siteId)) {
                $siteId = (int) $siteId;
            }
            if (\is_string($roleId) && ctype_digit($roleId)) {
                $roleId = (int) $roleId;
            }
            if (!\is_int($siteId) || $siteId <= 0 || !\is_int($roleId) || $roleId <= 0) {
                return null;
            }
            $out[] = ['siteId' => $siteId, 'roleId' => $roleId];
        }

        return $out;
    }
}
