<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\CreateSiteInput;
use PHPUnit\Framework\TestCase;

final class CreateSiteInputTest extends TestCase
{
    public function testValidPayload(): void
    {
        $input = CreateSiteInput::fromPayload([
            'name' => ' Blog ',
            'slug' => 'My-Blog',
            'enabled' => false,
        ]);

        self::assertTrue($input->isValid());
        self::assertSame('Blog', $input->name);
        self::assertSame('my-blog', $input->slug);
        self::assertFalse($input->enabled);
    }

    public function testDefaultsEnabledTrue(): void
    {
        $input = CreateSiteInput::fromPayload([
            'name' => 'Main',
            'slug' => 'main',
        ]);

        self::assertTrue($input->isValid());
        self::assertTrue($input->enabled);
    }

    public function testValidationErrors(): void
    {
        $input = CreateSiteInput::fromPayload([
            'name' => '',
            'slug' => 'Bad Slug!',
            'enabled' => 'maybe',
        ]);

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('name', $input->fieldErrors);
        self::assertArrayHasKey('slug', $input->fieldErrors);
        self::assertArrayHasKey('enabled', $input->fieldErrors);
    }

    public function testNonArrayBody(): void
    {
        $input = CreateSiteInput::fromPayload('nope');

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('_body', $input->fieldErrors);
    }
}
