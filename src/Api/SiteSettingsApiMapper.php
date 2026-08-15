<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\MediaAsset;
use App\Entity\Site;
use App\Entity\SiteAssignment;
use App\Entity\SiteHost;
use App\Repository\SiteAssignmentRepository;

final class SiteSettingsApiMapper
{
    public function __construct(
        private readonly SiteAssignmentRepository $assignments,
    ) {
    }

    /**
     * @param array{manageHosts: bool, manageUsers: bool} $capabilities
     *
     * @return array<string, mixed>
     */
    public function toArray(Site $site, array $capabilities): array
    {
        $hosts = [];
        /** @var SiteHost $host */
        foreach ($site->getHosts() as $host) {
            $hosts[] = [
                'id' => (int) $host->getId(),
                'host' => $host->getHost(),
                'surface' => $host->getSurface()->value,
                'verification' => $host->getVerification(),
                'enabled' => $host->isEnabled(),
                'protected' => $host->isProtected(),
            ];
        }

        $users = [];
        foreach ($this->assignments->findBySite($site) as $assignment) {
            $users[] = $this->assignmentToArray($assignment);
        }

        $favicon = $site->getFaviconMedia();

        return [
            'siteId' => (int) $site->getId(),
            'slug' => $site->getSlug(),
            'name' => $site->getName(),
            'description' => $site->getDescription(),
            'themeId' => $site->getThemeId(),
            'protected' => $site->isProtected(),
            'faviconMediaId' => $favicon instanceof MediaAsset ? (int) $favicon->getId() : null,
            'favicon' => $favicon instanceof MediaAsset && !$favicon->isDeleted()
                ? MediaAssetApiMapper::toArray($favicon)
                : null,
            'hosts' => $hosts,
            'assignments' => $users,
            'capabilities' => $capabilities,
        ];
    }

    /**
     * @return array{id: int, userId: int, email: string, roleId: int, roleName: string, roleLabel: string}
     */
    private function assignmentToArray(SiteAssignment $assignment): array
    {
        $user = $assignment->getUser();
        $role = $assignment->getRole();

        return [
            'id' => (int) $assignment->getId(),
            'userId' => (int) $user->getId(),
            'email' => $user->getEmail(),
            'roleId' => (int) $role->getId(),
            'roleName' => $role->getName(),
            'roleLabel' => $role->getLabel(),
        ];
    }
}
