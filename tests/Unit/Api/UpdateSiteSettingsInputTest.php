<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\UpdateSiteSettingsInput;
use PHPUnit\Framework\TestCase;

final class UpdateSiteSettingsInputTest extends TestCase
{
    public function testRequiresAtLeastOneField(): void
    {
        $input = UpdateSiteSettingsInput::fromPayload([]);
        self::assertFalse($input->isValid());
    }

    public function testParsesNameAndDescription(): void
    {
        $input = UpdateSiteSettingsInput::fromPayload([
            'name' => 'Acme',
            'description' => ' Hello ',
            'faviconMediaId' => null,
        ]);
        self::assertTrue($input->isValid());
        self::assertTrue($input->nameProvided);
        self::assertSame('Acme', $input->name);
        self::assertTrue($input->descriptionProvided);
        self::assertSame(' Hello ', $input->description);
        self::assertTrue($input->faviconMediaIdProvided);
        self::assertNull($input->faviconMediaId);
    }

    public function testRejectsEmptyName(): void
    {
        $input = UpdateSiteSettingsInput::fromPayload(['name' => '  ']);
        self::assertFalse($input->isValid());
        self::assertArrayHasKey('name', $input->fieldErrors);
    }
}
