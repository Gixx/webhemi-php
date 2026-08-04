<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;

final class HostUpdater
{
    public function __construct(
        private readonly SiteRepository $sites,
        private readonly EntityManagerInterface $em,
        private readonly HostUnassigner $unassigner,
    ) {
    }

    /**
     * @throws HostSiteNotFoundException
     */
    public function update(SiteHost $host, UpdateHostInput $input): SiteHost
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('UpdateHostInput must be valid before update().');
        }

        if (null !== $input->host) {
            $host->setHost($input->host);
        }

        if (null !== $input->surface) {
            $host->setSurface(SurfaceType::from($input->surface));
        }

        if (null !== $input->active) {
            $host->setIsActive($input->active);
        }

        if ($input->siteIdProvided) {
            if (null === $input->siteId) {
                $this->unassigner->unassign($host);

                return $host;
            }

            $site = $this->sites->find($input->siteId);
            if (!$site instanceof Site) {
                throw new HostSiteNotFoundException();
            }
            $host->setSite($site);
            if ('verified' === $host->getStatus()) {
                $host->setStatus('active');
            }
        }

        $this->em->flush();

        return $host;
    }
}
