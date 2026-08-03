<?php

declare(strict_types=1);

namespace App\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Stable JSON envelopes for /admin/api (Phase 6).
 *
 * Success: { "data": … }
 * Error:   { "error": { "code", "message", "fields?" } }
 */
final class ApiJson
{
    public static function data(mixed $data, int $status = 200): JsonResponse
    {
        return new JsonResponse(['data' => $data], $status);
    }

    /**
     * @param array<string, string> $fields
     */
    public static function error(
        string $code,
        string $message,
        int $status,
        array $fields = [],
    ): JsonResponse {
        $error = [
            'code' => $code,
            'message' => $message,
        ];
        if ([] !== $fields) {
            $error['fields'] = $fields;
        }

        return new JsonResponse(['error' => $error], $status);
    }
}
