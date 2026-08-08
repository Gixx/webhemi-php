<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use Doctrine\ORM\EntityManagerInterface;

final class HostUnassigner
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdminAccessModeResetter $accessModeResetter,
    ) {
    }

    /**
     * Detach host from its site without deleting the row.
     * Verification stays verified so the host can be assigned again.
     * Admin surface is cleared to site (admin requires a Main assignment).
     *
     * @throws HostProtectedException
     */
    public function unassign(SiteHost $host): HostMutationResult
    {
        if (!$host->getSite() instanceof Site) {
            return new HostMutationResult($host, false);
        }

        if ($host->isProtected()) {
            throw new HostProtectedException('Protected system host cannot be unassigned.');
        }

        $host->setSite(null);
        $host->setSurface(SurfaceType::Site);
        $this->em->flush();

        return new HostMutationResult(
            $host,
            $this->accessModeResetter->resetToPathIfNeeded(),
        );
    }
}
