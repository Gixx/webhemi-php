<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;

final class RoleDeleter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws RoleProtectedException
     * @throws RoleHasUsersException
     */
    public function delete(Role $role): void
    {
        if ($role->isProtected()) {
            throw new RoleProtectedException('Protected system role cannot be deleted.');
        }

        if (!$role->getUsers()->isEmpty()) {
            throw new RoleHasUsersException();
        }

        $role->clearPermissions();
        $this->em->remove($role);
        $this->em->flush();
    }
}
