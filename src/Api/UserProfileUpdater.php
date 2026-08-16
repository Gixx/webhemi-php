<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\User;
use App\Entity\UserLink;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class UserProfileUpdater
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly EntityManagerInterface $em,
        private readonly UserAvatarBlobStore $avatars,
    ) {
    }

    /**
     * @throws UserEmailTakenException
     */
    public function update(User $user, UpdateUserProfileInput $input): User
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('UpdateUserProfileInput must be valid before update().');
        }

        if ($input->emailProvided && null !== $input->email) {
            $other = $this->users->findOneBy(['email' => $input->email]);
            if ($other instanceof User && $other->getId() !== $user->getId()) {
                throw new UserEmailTakenException();
            }
            $user->setEmail($input->email);
        }

        if ($input->displayNameProvided) {
            $user->setDisplayName($input->displayName);
        }
        if ($input->telephoneProvided) {
            $user->setTelephone($input->telephone);
        }
        if ($input->addressProvided) {
            $user->setAddress($input->address);
        }
        if ($input->zipProvided) {
            $user->setZip($input->zip);
        }
        if ($input->cityProvided) {
            $user->setCity($input->city);
        }
        if ($input->countryProvided) {
            $user->setCountry($input->country);
        }
        if ($input->bioProvided) {
            $user->setBio($input->bio);
        }

        if ($input->avatarTypeProvided && null !== $input->avatarType) {
            $previousType = $user->getAvatarType();
            $previousPath = $user->getAvatarPath();
            $user->setAvatarType($input->avatarType);
            if (User::AVATAR_UPLOAD !== $input->avatarType) {
                $user->setAvatarPath(null);
                if (User::AVATAR_UPLOAD === $previousType) {
                    $this->avatars->deleteIfExists($previousPath);
                }
            }
        }

        if ($input->linksProvided && null !== $input->links) {
            $user->clearLinks();
            foreach ($input->links as $index => $row) {
                $link = (new UserLink())
                    ->setName($row['name'])
                    ->setUrl($row['url'])
                    ->setPosition($index);
                $user->addLink($link);
            }
        }

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            throw new UserEmailTakenException();
        }

        return $user;
    }
}
