<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\CreateHostInput;
use PHPUnit\Framework\TestCase;

final class CreateHostInputTest extends TestCase
{
    public function testValidPayload(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => ' WWW.Example.COM ',
            'siteId' => 3,
            'surface' => 'admin',
            'active' => false,
        ]);

        self::assertTrue($input->isValid());
        self::assertSame('www.example.com', $input->host);
        self::assertSame(3, $input->siteId);
        self::assertSame('admin', $input->surface);
        self::assertFalse($input->active);
    }

    public function testDefaults(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'blog.example.test',
            'siteId' => 1,
        ]);

        self::assertTrue($input->isValid());
        self::assertSame('site', $input->surface);
        self::assertTrue($input->active);
    }

    public function testValidationErrors(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => '',
            'siteId' => 'nope',
            'surface' => 'web',
            'active' => 'maybe',
        ]);

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('host', $input->fieldErrors);
        self::assertArrayHasKey('siteId', $input->fieldErrors);
        self::assertArrayHasKey('surface', $input->fieldErrors);
        self::assertArrayHasKey('active', $input->fieldErrors);
    }

    public function testInvalidHostnamePattern(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'not a host',
            'siteId' => 1,
        ]);

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('host', $input->fieldErrors);
    }

    public function testNonArrayBody(): void
    {
        $input = CreateHostInput::fromPayload('nope');

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('_body', $input->fieldErrors);
    }
}
