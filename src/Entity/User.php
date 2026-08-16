<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'app_user')]
#[ORM\UniqueConstraint(name: 'uniq_app_user_email', columns: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const AVATAR_DEFAULT = 'default';
    public const AVATAR_GRAVATAR = 'gravatar';
    public const AVATAR_UPLOAD = 'upload';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 191)]
    private string $email = '';

    #[ORM\Column(length: 255)]
    private string $passwordHash = '';

    #[ORM\Column(length: 16, options: ['default' => self::AVATAR_DEFAULT])]
    private string $avatarType = self::AVATAR_DEFAULT;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarPath = null;

    #[ORM\Column(length: 128)]
    private string $displayName = '';

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $zip = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    /** @var Collection<int, Role> */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_role')]
    private Collection $roles;

    /** @var Collection<int, SiteAssignment> */
    #[ORM\OneToMany(
        targetEntity: SiteAssignment::class,
        mappedBy: 'user',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $siteAssignments;

    /** @var Collection<int, UserLink> */
    #[ORM\OneToMany(
        targetEntity: UserLink::class,
        mappedBy: 'user',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    private Collection $links;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->siteAssignments = new ArrayCollection();
        $this->links = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = strtolower(trim($email));

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->passwordHash;
    }

    public function setPassword(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }

    public function getAvatarType(): string
    {
        if (!\in_array($this->avatarType, [self::AVATAR_DEFAULT, self::AVATAR_GRAVATAR, self::AVATAR_UPLOAD], true)) {
            return self::AVATAR_DEFAULT;
        }

        return $this->avatarType;
    }

    public function setAvatarType(string $avatarType): self
    {
        $type = strtolower(trim($avatarType));
        if (!\in_array($type, [self::AVATAR_DEFAULT, self::AVATAR_GRAVATAR, self::AVATAR_UPLOAD], true)) {
            $type = self::AVATAR_DEFAULT;
        }
        $this->avatarType = $type;

        return $this;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function setAvatarPath(?string $avatarPath): self
    {
        $this->avatarPath = null === $avatarPath || '' === trim($avatarPath)
            ? null
            : trim($avatarPath);

        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $trimmed = null === $displayName ? '' : trim($displayName);
        $this->displayName = $trimmed;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $trimmed = null === $telephone ? '' : trim($telephone);
        $this->telephone = '' === $trimmed ? null : $trimmed;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $trimmed = null === $address ? '' : trim($address);
        $this->address = '' === $trimmed ? null : $trimmed;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $trimmed = null === $zip ? '' : trim($zip);
        $this->zip = '' === $trimmed ? null : $trimmed;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $trimmed = null === $city ? '' : trim($city);
        $this->city = '' === $trimmed ? null : $trimmed;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $trimmed = null === $country ? '' : trim($country);
        $this->country = '' === $trimmed ? null : $trimmed;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        if (null === $bio) {
            $this->bio = null;

            return $this;
        }
        $trimmed = trim($bio);
        $this->bio = '' === $trimmed ? null : $bio;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $names = [];
        foreach ($this->roles as $role) {
            $names[] = $role->getName();
        }

        $names[] = 'ROLE_USER';

        return array_values(array_unique($names));
    }

    /** @return Collection<int, Role> */
    public function getRoleEntities(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /** @return Collection<int, SiteAssignment> */
    public function getSiteAssignments(): Collection
    {
        return $this->siteAssignments;
    }

    public function addSiteAssignment(SiteAssignment $assignment): self
    {
        if (!$this->siteAssignments->contains($assignment)) {
            $this->siteAssignments->add($assignment);
            $assignment->setUser($this);
        }

        return $this;
    }

    public function clearSiteAssignments(): self
    {
        $this->siteAssignments->clear();

        return $this;
    }

    /** @return Collection<int, UserLink> */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function addLink(UserLink $link): self
    {
        if (!$this->links->contains($link)) {
            $this->links->add($link);
            $link->setUser($this);
        }

        return $this;
    }

    public function clearLinks(): self
    {
        $this->links->clear();

        return $this;
    }

    public function clearRoles(): self
    {
        $this->roles->clear();

        return $this;
    }

    public function hasRoleName(string $name): bool
    {
        $name = strtoupper(trim($name));
        foreach ($this->roles as $role) {
            if ($role->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    public function eraseCredentials(): void
    {
    }
}
