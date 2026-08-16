<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserCreator
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly RoleRepository $roles,
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
        $user->setDisplayName($input->displayName);
        $user->setTelephone($input->telephone);
        $user->setAddress($input->address);
        $user->setZip($input->zip);
        $user->setCity($input->city);
        $user->setCountry($input->country);

        $roleIds = $input->roleIds;
        if ([] === $roleIds) {
            $guest = $this->roles->findOneBy(['name' => Role::GUEST]);
            if (!$guest instanceof Role || null === $guest->getId()) {
                throw new UserRoleNotFoundException();
            }
            $roleIds = [$guest->getId()];
        }

        $this->roleSync->syncGlobalRoles($user, $roleIds, enforceLastAdmin: false);
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
