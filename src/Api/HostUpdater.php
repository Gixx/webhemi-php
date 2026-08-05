<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use Doctrine\ORM\EntityManagerInterface;

final class HostUpdater
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HostUnassigner $unassigner,
        private readonly HostAssigner $assigner,
    ) {
    }

    /**
     * @throws HostSiteNotFoundException
     * @throws HostNotVerifiedForAssignException
     * @throws HostAlreadyAssignedException
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
            // Flush host/surface/active first via assign/unassign helpers.
            if (null === $input->siteId) {
                return $this->unassigner->unassign($host);
            }

            return $this->assigner->assign($host, $input->siteId);
        }

        $this->em->flush();

        return $host;
    }
}
