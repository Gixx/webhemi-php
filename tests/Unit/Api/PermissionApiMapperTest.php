<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\PermissionApiMapper;
use App\Entity\Permission;
use PHPUnit\Framework\TestCase;

final class PermissionApiMapperTest extends TestCase
{
    public function testToArray(): void
    {
        $permission = (new Permission())
            ->setName('content.edit')
            ->setLabel('Edit content')
            ->setDescription('Allows editing site content.');
        $ref = new \ReflectionProperty(Permission::class, 'id');
        $ref->setValue($permission, 3);

        self::assertSame(
            [
                'id' => 3,
                'name' => 'content.edit',
                'label' => 'Edit content',
                'description' => 'Allows editing site content.',
            ],
            PermissionApiMapper::toArray($permission),
        );
    }
}
