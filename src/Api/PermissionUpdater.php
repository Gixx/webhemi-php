<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Permission;
use App\Repository\PermissionRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class PermissionUpdater
{
    public function __construct(
        private readonly PermissionRepository $permissions,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws PermissionNameTakenException
     */
    public function update(Permission $permission, UpdatePermissionInput $input): Permission
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('UpdatePermissionInput must be valid before update().');
        }

        if ($input->nameProvided && null !== $input->name) {
            $other = $this->permissions->findOneBy(['name' => $input->name]);
            if ($other instanceof Permission && $other->getId() !== $permission->getId()) {
                throw new PermissionNameTakenException();
            }
            $permission->setName($input->name);
        }

        if ($input->labelProvided && null !== $input->label) {
            $permission->setLabel($input->label);
        }

        if ($input->descriptionProvided && null !== $input->description) {
            $permission->setDescription($input->description);
        }

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new PermissionNameTakenException(previous: $e);
        }

        return $permission;
    }
}
