<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;
use App\Entity\SiteHost;
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
     */
    public function unassign(SiteHost $host): SiteHost
    {
        if (!$host->getSite() instanceof Site) {
            return $host;
        }

        $host->setSite(null);
        $this->em->flush();
        $this->accessModeResetter->resetToPathIfNeeded();

        return $host;
    }
}
