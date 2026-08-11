<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserPasswordSetter
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws UserPasswordMismatchException
     */
    public function setPassword(User $user, SetUserPasswordInput $input): User
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('SetUserPasswordInput must be valid before setPassword().');
        }

        if ($input->requireCurrentPassword && !$this->passwordHasher->isPasswordValid($user, $input->currentPassword)) {
            throw new UserPasswordMismatchException();
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $input->password));
        $this->em->flush();

        return $user;
    }
}
