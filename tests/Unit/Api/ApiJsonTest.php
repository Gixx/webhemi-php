<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\ApiJson;
use PHPUnit\Framework\TestCase;

final class ApiJsonTest extends TestCase
{
    public function testDataEnvelope(): void
    {
        $response = ApiJson::data(['id' => 1], 201);
        $payload = json_decode((string) $response->getContent(), true);

        self::assertSame(201, $response->getStatusCode());
        self::assertSame(['data' => ['id' => 1]], $payload);
    }

    public function testErrorEnvelopeWithFields(): void
    {
        $response = ApiJson::error('validation_failed', 'Nope', 422, ['slug' => 'bad']);
        $payload = json_decode((string) $response->getContent(), true);

        self::assertSame(422, $response->getStatusCode());
        self::assertSame('validation_failed', $payload['error']['code']);
        self::assertSame(['slug' => 'bad'], $payload['error']['fields']);
    }
}
