<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\SiteAssignment;
use App\Entity\User;

final class UserApiMapper
{
    /**
     * @return array{
     *     id: int,
     *     email: string,
     *     displayName: string|null,
     *     telephone: string|null,
     *     address: string|null,
     *     zip: string|null,
     *     city: string|null,
     *     country: string|null,
     *     roleIds: list<int>,
     *     roles: list<array{id: int, name: string, label: string}>,
     *     siteAssignments: list<array{
     *         id: int,
     *         siteId: int,
     *         siteName: string,
     *         roleId: int,
     *         roleName: string,
     *         roleLabel: string
     *     }>,
     *     roleCount: int,
     *     siteAssignmentCount: int
     * }
     */
    public static function toArray(User $user): array
    {
        $roles = [];
        $roleIds = [];
        foreach ($user->getRoleEntities() as $role) {
            $id = $role->getId();
            if (null === $id) {
                continue;
            }
            $roleIds[] = $id;
            $roles[] = [
                'id' => $id,
                'name' => $role->getName(),
                'label' => $role->getLabel(),
            ];
        }
        usort($roles, static fn (array $a, array $b): int => $a['name'] <=> $b['name']);
        sort($roleIds);

        $assignments = [];
        foreach ($user->getSiteAssignments() as $assignment) {
            $assignments[] = self::assignmentToArray($assignment);
        }
        usort(
            $assignments,
            static fn (array $a, array $b): int => $a['siteName'] <=> $b['siteName'],
        );

        return [
            'id' => (int) $user->getId(),
            'email' => $user->getEmail(),
            'displayName' => $user->getDisplayName(),
            'telephone' => $user->getTelephone(),
            'address' => $user->getAddress(),
            'zip' => $user->getZip(),
            'city' => $user->getCity(),
            'country' => $user->getCountry(),
            'roleIds' => $roleIds,
            'roles' => $roles,
            'siteAssignments' => $assignments,
            'roleCount' => \count($roleIds),
            'siteAssignmentCount' => \count($assignments),
        ];
    }

    /**
     * @return array{
     *     id: int,
     *     siteId: int,
     *     siteName: string,
     *     roleId: int,
     *     roleName: string,
     *     roleLabel: string
     * }
     */
    private static function assignmentToArray(SiteAssignment $assignment): array
    {
        $site = $assignment->getSite();
        $role = $assignment->getRole();

        return [
            'id' => (int) $assignment->getId(),
            'siteId' => (int) $site->getId(),
            'siteName' => $site->getName(),
            'roleId' => (int) $role->getId(),
            'roleName' => $role->getName(),
            'roleLabel' => $role->getLabel(),
        ];
    }
}
