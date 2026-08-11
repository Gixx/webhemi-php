<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Actor capabilities for Users CP / My Account.
 *
 * @see docs/plan/Users_RBAC_and_My_Account.md
 */
final class UserAccess
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $auth,
    ) {
    }

    public function isAdmin(User $actor): bool
    {
        return \in_array(Role::ADMIN, $actor->getRoles(), true);
    }

    public function canListUsers(User $actor): bool
    {
        return $this->isAdmin($actor) || $this->auth->isGranted('user.list');
    }

    public function canViewUser(User $actor, User $target): bool
    {
        if ($actor->getId() !== null && $actor->getId() === $target->getId()) {
            return true;
        }

        return $this->isAdmin($actor) || $this->auth->isGranted('user.view');
    }

    public function canCreateUser(User $actor): bool
    {
        return $this->isAdmin($actor) || $this->auth->isGranted('user.create');
    }

    public function canEditUser(User $actor): bool
    {
        return $this->isAdmin($actor) || $this->auth->isGranted('user.edit');
    }

    public function canDeleteUser(User $actor): bool
    {
        return $this->isAdmin($actor) || $this->auth->isGranted('user.delete');
    }

    /** Self always; other users need edit (or Admin). */
    public function canSetPassword(User $actor, User $target): bool
    {
        if ($actor->getId() !== null && $actor->getId() === $target->getId()) {
            return true;
        }

        return $this->canEditUser($actor);
    }

    /**
     * @return array{
     *     listUsers: bool,
     *     viewUser: bool,
     *     createUser: bool,
     *     editUser: bool,
     *     deleteUser: bool
     * }
     */
    public function capabilities(User $actor): array
    {
        return [
            'listUsers' => $this->canListUsers($actor),
            'viewUser' => $this->isAdmin($actor) || $this->auth->isGranted('user.view'),
            'createUser' => $this->canCreateUser($actor),
            'editUser' => $this->canEditUser($actor),
            'deleteUser' => $this->canDeleteUser($actor),
        ];
    }
}
