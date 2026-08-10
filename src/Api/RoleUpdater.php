<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Permission;
use App\Entity\Role;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class RoleUpdater
{
    public function __construct(
        private readonly RoleRepository $roles,
        private readonly PermissionRepository $permissions,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws RoleProtectedException
     * @throws RoleNameTakenException
     * @throws RolePermissionNotFoundException
     */
    public function update(Role $role, UpdateRoleInput $input): Role
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('UpdateRoleInput must be valid before update().');
        }

        if ($role->isProtected()) {
            throw new RoleProtectedException('Protected system role cannot be edited.');
        }

        if ($input->nameProvided && null !== $input->name) {
            $other = $this->roles->findOneBy(['name' => $input->name]);
            if ($other instanceof Role && $other->getId() !== $role->getId()) {
                throw new RoleNameTakenException();
            }
            $role->setName($input->name);
        }

        if ($input->labelProvided && null !== $input->label) {
            $role->setLabel($input->label);
        }

        if ($input->descriptionProvided && null !== $input->description) {
            $role->setDescription($input->description);
        }

        if ($input->permissionIdsProvided && null !== $input->permissionIds) {
            $this->syncPermissions($role, $input->permissionIds);
        }

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new RoleNameTakenException(previous: $e);
        }

        return $role;
    }

    /**
     * @param list<int> $permissionIds
     *
     * @throws RolePermissionNotFoundException
     */
    private function syncPermissions(Role $role, array $permissionIds): void
    {
        $role->clearPermissions();
        foreach ($permissionIds as $id) {
            $permission = $this->permissions->find($id);
            if (!$permission instanceof Permission) {
                throw new RolePermissionNotFoundException();
            }
            $role->addPermission($permission);
        }
    }
}
