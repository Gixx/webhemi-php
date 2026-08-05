<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;

final class HostAssigner
{
    public function __construct(
        private readonly SiteRepository $sites,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Bind a verified, unassigned host to a site and set status active.
     *
     * @throws HostNotVerifiedForAssignException
     * @throws HostAlreadyAssignedException
     * @throws HostSiteNotFoundException
     */
    public function assign(SiteHost $host, int $siteId): SiteHost
    {
        if ('verified' !== $host->getStatus()) {
            throw new HostNotVerifiedForAssignException();
        }

        if ($host->getSite() instanceof Site) {
            throw new HostAlreadyAssignedException();
        }

        $site = $this->sites->find($siteId);
        if (!$site instanceof Site) {
            throw new HostSiteNotFoundException();
        }

        $host->setSite($site);
        $host->setStatus('active');
        $this->em->flush();

        return $host;
    }
}
