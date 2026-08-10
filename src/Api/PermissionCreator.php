<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Permission;
use App\Repository\PermissionRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class PermissionCreator
{
    public function __construct(
        private readonly PermissionRepository $permissions,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws PermissionNameTakenException
     */
    public function create(CreatePermissionInput $input): Permission
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('CreatePermissionInput must be valid before create().');
        }

        if ($this->permissions->findOneBy(['name' => $input->name]) instanceof Permission) {
            throw new PermissionNameTakenException();
        }

        $permission = (new Permission())
            ->setName($input->name)
            ->setLabel($input->label)
            ->setDescription($input->description);

        $this->em->persist($permission);

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new PermissionNameTakenException(previous: $e);
        }

        return $permission;
    }
}
