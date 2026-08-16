<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\User;
use App\Entity\UserLink;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UserProfileApiMapper
{
    public function __construct(
        private readonly UrlGeneratorInterface $urls,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(User $user): array
    {
        $links = [];
        foreach ($user->getLinks() as $link) {
            $links[] = [
                'id' => (int) $link->getId(),
                'name' => $link->getName(),
                'url' => $link->getUrl(),
                'position' => $link->getPosition(),
            ];
        }

        return [
            'id' => (int) $user->getId(),
            'email' => $user->getEmail(),
            'displayName' => '' === $user->getDisplayName() ? null : $user->getDisplayName(),
            'telephone' => $user->getTelephone(),
            'address' => $user->getAddress(),
            'zip' => $user->getZip(),
            'city' => $user->getCity(),
            'country' => $user->getCountry(),
            'bio' => $user->getBio(),
            'avatarType' => $user->getAvatarType(),
            'avatarUrl' => $this->avatarUrl($user),
            'links' => $links,
        ];
    }

    public function avatarUrl(User $user): ?string
    {
        return match ($user->getAvatarType()) {
            User::AVATAR_UPLOAD => null !== $user->getAvatarPath()
                ? $this->urls->generate('api_admin_me_avatar', [], UrlGeneratorInterface::ABSOLUTE_PATH)
                : null,
            User::AVATAR_GRAVATAR => self::gravatarUrl($user->getEmail()),
            default => null,
        };
    }

    public static function gravatarUrl(string $email, int $size = 150): string
    {
        $hash = md5(strtolower(trim($email)));

        return sprintf('https://gravatar.com/avatar/%s?s=%d&d=identicon', $hash, $size);
    }
}
