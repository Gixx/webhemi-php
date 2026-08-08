<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;

final class HostAssigner
{
    public function __construct(
        private readonly SiteRepository $sites,
        private readonly SiteHostRepository $hosts,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Bind a verified, unassigned host to a site.
     *
     * @throws HostNotVerifiedForAssignException
     * @throws HostAlreadyAssignedException
     * @throws HostSiteNotFoundException
     * @throws HostAdminSurfaceNotAllowedException
     */
    public function assign(SiteHost $host, int $siteId): SiteHost
    {
        if ('verified' !== $host->getVerification()) {
            throw new HostNotVerifiedForAssignException();
        }

        if ($host->getSite() instanceof Site) {
            throw new HostAlreadyAssignedException();
        }

        $site = $this->sites->find($siteId);
        if (!$site instanceof Site) {
            throw new HostSiteNotFoundException();
        }

        if (SurfaceType::Admin === $host->getSurface()) {
            $existing = $this->hosts->findAdminSurfaceHost();
            if (!HostAdminSurfaceRules::canClaimAdminSurface($site, $host->getId(), $existing)) {
                $message = HostAdminSurfaceRules::allowsAdminSurface($site)
                    ? 'An admin surface host already exists.'
                    : 'Admin surface is only allowed on the Main site.';

                throw new HostAdminSurfaceNotAllowedException($message);
            }
        }

        $host->setSite($site);
        $this->em->flush();

        return $host;
    }
}
