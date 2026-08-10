<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\RoleApiMapper;
use App\Entity\Permission;
use App\Entity\Role;
use PHPUnit\Framework\TestCase;

final class RoleApiMapperTest extends TestCase
{
    public function testToArray(): void
    {
        $perm = (new Permission())->setName('content.edit')->setLabel('Edit');
        $permId = new \ReflectionProperty(Permission::class, 'id');
        $permId->setValue($perm, 9);

        $role = (new Role())
            ->setName('ROLE_AUTHOR')
            ->setLabel('Author')
            ->setIsReadOnly(false)
            ->addPermission($perm);
        $roleId = new \ReflectionProperty(Role::class, 'id');
        $roleId->setValue($role, 4);

        self::assertSame(
            [
                'id' => 4,
                'name' => 'ROLE_AUTHOR',
                'label' => 'Author',
                'description' => '',
                'protected' => false,
                'permissionIds' => [9],
                'permissionCount' => 1,
            ],
            RoleApiMapper::toArray($role),
        );
    }
}
