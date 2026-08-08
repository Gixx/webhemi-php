<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\CreateHostInput;
use PHPUnit\Framework\TestCase;

final class CreateHostInputTest extends TestCase
{
    public function testRejectsSiteIdOnCreate(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'www.example.com',
            'siteId' => 3,
            'surface' => 'admin',
            'enabled' => false,
        ]);

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('siteId', $input->fieldErrors);
        self::assertNull($input->siteId);
    }

    public function testValidPayloadWithoutSite(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'blog.example.test',
        ]);

        self::assertTrue($input->isValid());
        self::assertNull($input->siteId);
        self::assertSame('site', $input->surface);
        self::assertTrue($input->enabled);
    }

    public function testExplicitNullSiteId(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'blog.example.test',
            'siteId' => null,
        ]);

        self::assertTrue($input->isValid());
        self::assertNull($input->siteId);
    }

    public function testValidationErrors(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => '',
            'siteId' => 'nope',
            'surface' => 'web',
            'enabled' => 'maybe',
        ]);

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('host', $input->fieldErrors);
        self::assertArrayHasKey('siteId', $input->fieldErrors);
        self::assertArrayHasKey('surface', $input->fieldErrors);
        self::assertArrayHasKey('enabled', $input->fieldErrors);
    }

    public function testInvalidHostnamePattern(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'not a host',
        ]);

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('host', $input->fieldErrors);
    }

    public function testRejectsApiSurface(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'api.example.com',
            'surface' => 'api',
        ]);

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('surface', $input->fieldErrors);
        self::assertSame('site', $input->surface);
    }

    public function testNonArrayBody(): void
    {
        $input = CreateHostInput::fromPayload('nope');

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('_body', $input->fieldErrors);
    }
}
