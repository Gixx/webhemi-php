<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\CreateRoleInput;
use App\Api\RoleCreator;
use App\Api\RoleNameTakenException;
use App\Entity\Permission;
use App\Entity\Role;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class RoleCreatorTest extends TestCase
{
    public function testCreatesRoleWithPermissions(): void
    {
        $input = CreateRoleInput::fromPayload([
            'name' => 'role_author',
            'label' => 'Author',
            'description' => 'Can edit and publish content.',
            'permissionIds' => [3],
        ]);
        self::assertTrue($input->isValid());
        self::assertSame('ROLE_AUTHOR', $input->name);
        self::assertSame('Can edit and publish content.', $input->description);

        $perm = (new Permission())->setName('content.edit')->setLabel('Edit');
        $permRef = new \ReflectionProperty(Permission::class, 'id');
        $permRef->setValue($perm, 3);

        $roles = $this->createMock(RoleRepository::class);
        $roles->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'ROLE_AUTHOR'])
            ->willReturn(null);

        $permissions = $this->createMock(PermissionRepository::class);
        $permissions->expects(self::once())
            ->method('find')
            ->with(3)
            ->willReturn($perm);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist')->with(self::isInstanceOf(Role::class));
        $em->expects(self::once())->method('flush');

        $role = (new RoleCreator($roles, $permissions, $em))->create($input);

        self::assertSame('ROLE_AUTHOR', $role->getName());
        self::assertSame('Can edit and publish content.', $role->getDescription());
        self::assertTrue($role->hasPermission('content.edit'));
        self::assertFalse($role->isProtected());
    }

    public function testReservedSystemNameRejected(): void
    {
        $input = CreateRoleInput::fromPayload([
            'name' => 'ROLE_ADMIN',
            'label' => 'Admin',
        ]);
        self::assertFalse($input->isValid());
        self::assertArrayHasKey('name', $input->fieldErrors);
    }

    public function testDuplicateNameThrows(): void
    {
        $input = CreateRoleInput::fromPayload([
            'name' => 'ROLE_AUTHOR',
            'label' => 'Author',
        ]);
        $existing = (new Role())->setName('ROLE_AUTHOR')->setLabel('Existing');

        $roles = $this->createStub(RoleRepository::class);
        $roles->method('findOneBy')->willReturn($existing);

        $permissions = $this->createStub(PermissionRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');

        $this->expectException(RoleNameTakenException::class);
        (new RoleCreator($roles, $permissions, $em))->create($input);
    }
}
