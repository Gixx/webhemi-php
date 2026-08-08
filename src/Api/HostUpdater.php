<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteHostRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class HostUpdater
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SiteHostRepository $hosts,
        private readonly HostUnassigner $unassigner,
        private readonly HostAssigner $assigner,
    ) {
    }

    /**
     * @throws HostHostTakenException
     * @throws HostSiteNotFoundException
     * @throws HostNotVerifiedForAssignException
     * @throws HostAlreadyAssignedException
     * @throws HostAdminSurfaceNotAllowedException
     */
    public function update(SiteHost $host, UpdateHostInput $input): SiteHost
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('UpdateHostInput must be valid before update().');
        }

        if (null !== $input->host) {
            $other = $this->hosts->findOneBy(['host' => $input->host]);
            if ($other instanceof SiteHost && $other->getId() !== $host->getId()) {
                throw new HostHostTakenException();
            }
            $host->setHost($input->host);
        }

        if (null !== $input->surface) {
            $newSurface = SurfaceType::from($input->surface);
            $assigningToSite = $input->siteIdProvided && null !== $input->siteId;
            $unassigning = $input->siteIdProvided && null === $input->siteId;
            $siteForCheck = $unassigning ? null : $host->getSite();
            // Assign path validates the target site in HostAssigner after surface is set.
            if (
                !$assigningToSite
                && SurfaceType::Admin === $newSurface
                && !HostAdminSurfaceRules::allowsAdminSurface($siteForCheck)
            ) {
                throw new HostAdminSurfaceNotAllowedException();
            }
            $host->setSurface($newSurface);
        }

        if (null !== $input->enabled) {
            $host->setIsEnabled($input->enabled);
        }

        if ($input->siteIdProvided) {
            if (null === $input->siteId) {
                return $this->unassigner->unassign($host);
            }

            $currentSiteId = $host->getSite()?->getId();
            if ($currentSiteId === $input->siteId) {
                // Same site already bound — do not re-assign.
                try {
                    $this->em->flush();
                } catch (UniqueConstraintViolationException $e) {
                    throw new HostHostTakenException(previous: $e);
                }

                return $host;
            }

            return $this->assigner->assign($host, $input->siteId);
        }

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new HostHostTakenException(previous: $e);
        }

        return $host;
    }
}
