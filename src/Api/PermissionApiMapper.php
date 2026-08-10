<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Permission;

final class PermissionApiMapper
{
    /**
     * @return array{id: int, name: string, label: string, description: string}
     */
    public static function toArray(Permission $permission): array
    {
        return [
            'id' => (int) $permission->getId(),
            'name' => $permission->getName(),
            'label' => $permission->getLabel(),
            'description' => $permission->getDescription(),
        ];
    }
}
