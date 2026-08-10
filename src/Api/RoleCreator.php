<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Permission;
use App\Entity\Role;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class RoleCreator
{
    public function __construct(
        private readonly RoleRepository $roles,
        private readonly PermissionRepository $permissions,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws RoleNameTakenException
     * @throws RolePermissionNotFoundException
     */
    public function create(CreateRoleInput $input): Role
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('CreateRoleInput must be valid before create().');
        }

        if ($this->roles->findOneBy(['name' => $input->name]) instanceof Role) {
            throw new RoleNameTakenException();
        }

        $role = (new Role())
            ->setName($input->name)
            ->setLabel($input->label)
            ->setDescription($input->description)
            ->setIsReadOnly(false);

        $this->syncPermissions($role, $input->permissionIds);
        $this->em->persist($role);

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
