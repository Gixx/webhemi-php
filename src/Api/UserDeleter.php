<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class UserDeleter
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws UserSelfDeleteException
     * @throws UserLastAdminException
     */
    public function delete(User $user, ?User $actor): void
    {
        if ($actor instanceof User && null !== $actor->getId() && $actor->getId() === $user->getId()) {
            throw new UserSelfDeleteException();
        }

        if ($user->hasRoleName(Role::ADMIN)) {
            $others = $this->users->countAdmins($user->getId());
            if (0 === $others) {
                throw new UserLastAdminException('Cannot delete the last Administrator account.');
            }
        }

        $user->clearRoles();
        $user->clearSiteAssignments();
        $this->em->remove($user);
        $this->em->flush();
    }
}
