<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Role;

final class RoleApiMapper
{
    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     label: string,
     *     description: string,
     *     protected: bool,
     *     permissionIds: list<int>,
     *     permissionCount: int
     * }
     */
    public static function toArray(Role $role): array
    {
        $permissionIds = [];
        foreach ($role->getPermissions() as $permission) {
            $id = $permission->getId();
            if (null !== $id) {
                $permissionIds[] = $id;
            }
        }
        sort($permissionIds);

        return [
            'id' => (int) $role->getId(),
            'name' => $role->getName(),
            'label' => $role->getLabel(),
            'description' => $role->getDescription(),
            'protected' => $role->isProtected(),
            'permissionIds' => $permissionIds,
            'permissionCount' => \count($permissionIds),
        ];
    }
}
