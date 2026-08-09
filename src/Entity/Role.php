<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: 'rbac_role')]
#[ORM\UniqueConstraint(name: 'uniq_rbac_role_name', columns: ['name'])]
class Role
{
    public const ADMIN = 'ROLE_ADMIN';
    public const SITE_ADMIN = 'ROLE_SITE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $name = '';

    #[ORM\Column(length: 128)]
    private string $label = '';

    /** System roles (Admin, Site Admin): not deletable / not editable in product UI. */
    #[ORM\Column(name: 'is_read_only')]
    private bool $isReadOnly = false;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
    private Collection $users;

    /** @var Collection<int, Permission> */
    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'roles')]
    #[ORM\JoinTable(name: 'role_permission')]
    private Collection $permissions;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = strtoupper(trim($name));

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = trim($label);

        return $this;
    }

    public function isReadOnly(): bool
    {
        return $this->isReadOnly;
    }

    public function setIsReadOnly(bool $isReadOnly): self
    {
        $this->isReadOnly = $isReadOnly;

        return $this;
    }

    /** Product synonym for {@see isReadOnly()} (locked system role). */
    public function isProtected(): bool
    {
        return $this->isReadOnly;
    }

    public function setIsProtected(bool $isProtected): self
    {
        return $this->setIsReadOnly($isProtected);
    }

    public function isAdmin(): bool
    {
        return self::ADMIN === $this->name;
    }

    public function isSiteAdmin(): bool
    {
        return self::SITE_ADMIN === $this->name;
    }

    /** @return Collection<int, Permission> */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function hasPermission(string $permission): bool
    {
        $normalized = strtolower(trim($permission));
        foreach ($this->permissions as $assigned) {
            if ($assigned->getName() === $normalized) {
                return true;
            }
        }

        return false;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        $this->permissions->removeElement($permission);

        return $this;
    }
}
