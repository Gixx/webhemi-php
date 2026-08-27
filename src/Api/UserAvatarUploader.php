<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UserAvatarUploader
{
    private const MAX_BYTES = 512_000;

    public function __construct(
        private readonly UserAvatarBlobStore $avatars,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function upload(User $user, UploadedFile $file): User
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException(UploadedFileErrors::message($file));
        }
        if ($file->getSize() > self::MAX_BYTES) {
            throw new \InvalidArgumentException('Avatar must be 512 KB or smaller.');
        }

        $mime = $file->getMimeType() ?: '';
        if (!\in_array($mime, ['image/jpeg', 'image/jpg'], true)) {
            throw new \InvalidArgumentException('Avatar must be a JPEG image.');
        }

        $stored = $this->avatars->storeFromPath($file->getPathname());
        $previous = $user->getAvatarPath();
        $user->setAvatarType(User::AVATAR_UPLOAD);
        $user->setAvatarPath($stored['storageKey']);
        $this->em->flush();

        if ($previous !== $stored['storageKey']) {
            $this->avatars->deleteIfExists($previous);
        }

        return $user;
    }
}
