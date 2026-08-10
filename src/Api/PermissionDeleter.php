<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Permission;
use Doctrine\ORM\EntityManagerInterface;

final class PermissionDeleter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws PermissionHasRolesException
     */
    public function delete(Permission $permission): void
    {
        if (!$permission->getRoles()->isEmpty()) {
            throw new PermissionHasRolesException();
        }

        $this->em->remove($permission);
        $this->em->flush();
    }
}
