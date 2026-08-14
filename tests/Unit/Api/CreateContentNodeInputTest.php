<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\CreateContentNodeInput;
use PHPUnit\Framework\TestCase;

final class CreateContentNodeInputTest extends TestCase
{
    public function testValidFolder(): void
    {
        $input = CreateContentNodeInput::fromPayload([
            'tree' => 'site',
            'kind' => 'folder',
            'slug' => 'About-Us',
            'title' => ' About ',
            'publication' => 'draft',
            'hidden' => true,
        ]);

        self::assertTrue($input->isValid());
        self::assertSame('about-us', $input->slug);
        self::assertSame('About', $input->title);
        self::assertSame('normal', $input->folderType);
        self::assertTrue($input->hidden);
    }

    public function testReservedFieldsValidated(): void
    {
        $input = CreateContentNodeInput::fromPayload([
            'kind' => 'redirect',
            'slug' => 'go',
            'title' => 'Go',
        ]);

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('redirectTarget', $input->fieldErrors);
    }

    public function testScheduledNeedsPublishAt(): void
    {
        $input = CreateContentNodeInput::fromPayload([
            'kind' => 'document',
            'slug' => 'post',
            'title' => 'Post',
            'publication' => 'scheduled',
        ]);

        self::assertFalse($input->isValid());
        self::assertArrayHasKey('publishAt', $input->fieldErrors);
    }
}
