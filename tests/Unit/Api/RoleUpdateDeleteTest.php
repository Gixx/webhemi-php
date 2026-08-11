<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\RoleDeleter;
use App\Api\RoleHasUsersException;
use App\Api\RoleProtectedException;
use App\Api\RoleUpdater;
use App\Api\UpdateRoleInput;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use App\Repository\SiteAssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class RoleUpdateDeleteTest extends TestCase
{
    public function testUpdateChangesLabel(): void
    {
        $role = (new Role())->setName('ROLE_AUTHOR')->setLabel('Author');
        $ref = new \ReflectionProperty(Role::class, 'id');
        $ref->setValue($role, 1);

        $input = UpdateRoleInput::fromPayload(['label' => 'Content author']);
        self::assertTrue($input->isValid());

        $roles = $this->createStub(RoleRepository::class);
        $permissions = $this->createStub(PermissionRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $updated = (new RoleUpdater($roles, $permissions, $em))->update($role, $input);
        self::assertSame('Content author', $updated->getLabel());
    }

    public function testUpdateChangesDescription(): void
    {
        $role = (new Role())->setName('ROLE_AUTHOR')->setLabel('Author');
        $ref = new \ReflectionProperty(Role::class, 'id');
        $ref->setValue($role, 1);

        $input = UpdateRoleInput::fromPayload(['description' => 'Writes posts.']);
        self::assertTrue($input->isValid());

        $roles = $this->createStub(RoleRepository::class);
        $permissions = $this->createStub(PermissionRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $updated = (new RoleUpdater($roles, $permissions, $em))->update($role, $input);
        self::assertSame('Writes posts.', $updated->getDescription());
    }

    public function testUpdateProtectedThrows(): void
    {
        $role = (new Role())
            ->setName(Role::ADMIN)
            ->setLabel('Administrator')
            ->setIsReadOnly(true);

        $input = UpdateRoleInput::fromPayload(['label' => 'Nope']);
        $roles = $this->createStub(RoleRepository::class);
        $permissions = $this->createStub(PermissionRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $this->expectException(RoleProtectedException::class);
        (new RoleUpdater($roles, $permissions, $em))->update($role, $input);
    }

    public function testDeleteProtectedThrows(): void
    {
        $role = (new Role())
            ->setName(Role::SITE_ADMIN)
            ->setLabel('Site Administrator')
            ->setIsReadOnly(true);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('remove');

        $this->expectException(RoleProtectedException::class);
        (new RoleDeleter($em, $this->createStub(SiteAssignmentRepository::class)))->delete($role);
    }

    public function testDeleteWithUsersThrows(): void
    {
        $role = (new Role())->setName('ROLE_AUTHOR')->setLabel('Author');
        $user = (new User());
        $role->getUsers()->add($user);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('remove');

        $this->expectException(RoleHasUsersException::class);
        (new RoleDeleter($em, $this->createStub(SiteAssignmentRepository::class)))->delete($role);
    }

    public function testDeleteCustomSucceeds(): void
    {
        $role = (new Role())->setName('ROLE_AUTHOR')->setLabel('Author');

        $assignments = $this->createMock(SiteAssignmentRepository::class);
        $assignments->expects(self::once())->method('count')->with(['role' => $role])->willReturn(0);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('remove')->with($role);
        $em->expects(self::once())->method('flush');

        (new RoleDeleter($em, $assignments))->delete($role);
    }
}
