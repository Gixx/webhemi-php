<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\PermissionDeleter;
use App\Api\PermissionHasRolesException;
use App\Api\PermissionUpdater;
use App\Api\UpdatePermissionInput;
use App\Entity\Permission;
use App\Entity\Role;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class PermissionUpdateDeleteTest extends TestCase
{
    public function testUpdateChangesFields(): void
    {
        $permission = (new Permission())
            ->setName('content.edit')
            ->setLabel('Edit')
            ->setDescription('');
        $ref = new \ReflectionProperty(Permission::class, 'id');
        $ref->setValue($permission, 1);

        $input = UpdatePermissionInput::fromPayload([
            'label' => 'Edit content',
            'description' => 'Updated help.',
        ]);
        self::assertTrue($input->isValid());

        $permissions = $this->createStub(PermissionRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $updated = (new PermissionUpdater($permissions, $em))->update($permission, $input);

        self::assertSame('Edit content', $updated->getLabel());
        self::assertSame('Updated help.', $updated->getDescription());
    }

    public function testDeleteWithRolesThrows(): void
    {
        $permission = (new Permission())->setName('content.edit')->setLabel('Edit');
        $role = (new Role())->setName('ROLE_AUTHOR')->setLabel('Author');
        // Inverse collection is not auto-synced from Role::addPermission in unit tests.
        $permission->getRoles()->add($role);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('remove');

        $this->expectException(PermissionHasRolesException::class);
        (new PermissionDeleter($em))->delete($permission);
    }

    public function testDeleteUnassignedSucceeds(): void
    {
        $permission = (new Permission())->setName('content.edit')->setLabel('Edit');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('remove')->with($permission);
        $em->expects(self::once())->method('flush');

        (new PermissionDeleter($em))->delete($permission);
    }
}
