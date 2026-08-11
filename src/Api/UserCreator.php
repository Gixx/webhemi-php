<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserCreator
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly UserRoleSync $roleSync,
        private readonly UserPasswordHasherInterface $passwordHasher,
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
    public function create(CreateUserInput $input): User
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('CreateUserInput must be valid before create().');
        }

        if ($this->users->findOneBy(['email' => $input->email]) instanceof User) {
            throw new UserEmailTakenException();
        }

        $user = (new User())->setEmail($input->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $input->password));

        $this->roleSync->syncGlobalRoles($user, $input->roleIds, enforceLastAdmin: false);
        $this->roleSync->syncSiteAssignments($user, $input->siteAssignments);

        $this->em->persist($user);

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new UserEmailTakenException(previous: $e);
        }

        return $user;
    }
}
