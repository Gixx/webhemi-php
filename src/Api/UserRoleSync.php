<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Role;
use App\Entity\Site;
use App\Entity\SiteAssignment;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;

/**
 * Shared role / site_assignment sync rules for Users API.
 */
final class UserRoleSync
{
    public function __construct(
        private readonly RoleRepository $roles,
        private readonly SiteRepository $sites,
        private readonly UserRepository $users,
    ) {
    }

    /**
     * @param list<int> $roleIds
     *
     * @throws UserRoleNotFoundException
     * @throws UserInvalidRoleException
     * @throws UserLastAdminException
     */
    public function syncGlobalRoles(User $user, array $roleIds, bool $enforceLastAdmin): void
    {
        if ([] === $roleIds) {
            throw new UserInvalidRoleException('At least one role is required.');
        }

        $resolved = [];
        $willHaveAdmin = false;
        foreach ($roleIds as $id) {
            $role = $this->roles->find($id);
            if (!$role instanceof Role) {
                throw new UserRoleNotFoundException();
            }
            if (Role::SITE_ADMIN === $role->getName()) {
                throw new UserInvalidRoleException(
                    'Site Admin cannot be assigned as a global role; use siteAssignments.',
                );
            }
            if (Role::ADMIN === $role->getName()) {
                $willHaveAdmin = true;
            }
            $resolved[] = $role;
        }

        if ($enforceLastAdmin && $user->hasRoleName(Role::ADMIN) && !$willHaveAdmin) {
            $others = $this->users->countAdmins($user->getId());
            if (0 === $others) {
                throw new UserLastAdminException(
                    'Cannot remove Administrator from the last Administrator account.',
                );
            }
        }

        $user->clearRoles();
        foreach ($resolved as $role) {
            $user->addRole($role);
        }
    }

    /**
     * @param list<array{siteId: int, roleId: int}> $assignments
     *
     * @throws UserSiteNotFoundException
     * @throws UserRoleNotFoundException
     * @throws UserInvalidRoleException
     */
    public function syncSiteAssignments(User $user, array $assignments): void
    {
        $user->clearSiteAssignments();

        foreach ($assignments as $row) {
            $site = $this->sites->find($row['siteId']);
            if (!$site instanceof Site) {
                throw new UserSiteNotFoundException();
            }
            $role = $this->roles->find($row['roleId']);
            if (!$role instanceof Role) {
                throw new UserRoleNotFoundException();
            }
            if (Role::ADMIN === $role->getName()) {
                throw new UserInvalidRoleException(
                    'Administrator cannot be used as a site assignment role.',
                );
            }

            $assignment = (new SiteAssignment())
                ->setSite($site)
                ->setRole($role);
            $user->addSiteAssignment($assignment);
        }
    }
}
