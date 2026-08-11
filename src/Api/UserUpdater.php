<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class UserUpdater
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly UserRoleSync $roleSync,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws UserEmailTakenException
     * @throws UserRoleNotFoundException
     * @throws UserSiteNotFoundException
     * @throws UserInvalidRoleException
     * @throws UserLastAdminException
     */
    public function update(User $user, UpdateUserInput $input): User
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('UpdateUserInput must be valid before update().');
        }

        if ($input->emailProvided && null !== $input->email) {
            $other = $this->users->findOneBy(['email' => $input->email]);
            if ($other instanceof User && $other->getId() !== $user->getId()) {
                throw new UserEmailTakenException();
            }
            $user->setEmail($input->email);
        }

        if ($input->roleIdsProvided && null !== $input->roleIds) {
            $this->roleSync->syncGlobalRoles($user, $input->roleIds, enforceLastAdmin: true);
        }

        if ($input->siteAssignmentsProvided && null !== $input->siteAssignments) {
            $this->roleSync->syncSiteAssignments($user, $input->siteAssignments);
        }

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new UserEmailTakenException(previous: $e);
        }

        return $user;
    }
}
