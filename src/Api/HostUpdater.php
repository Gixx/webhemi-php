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
        private readonly AdminAccessModeResetter $accessModeResetter,
    ) {
    }

    /**
     * @throws HostHostTakenException
     * @throws HostSiteNotFoundException
     * @throws HostNotVerifiedForAssignException
     * @throws HostAlreadyAssignedException
     * @throws HostAdminSurfaceNotAllowedException
     * @throws HostProtectedException
     */
    public function update(SiteHost $host, UpdateHostInput $input): HostMutationResult
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('UpdateHostInput must be valid before update().');
        }

        $this->assertProtectedMutable($host, $input);

        if (null !== $input->host) {
            $other = $this->hosts->findOneBy(['host' => $input->host]);
            if ($other instanceof SiteHost && $other->getId() !== $host->getId()) {
                throw new HostHostTakenException();
            }
            if ($input->host !== $host->getHost()) {
                $host->setHost($input->host);
                // Ownership must be proven for the new hostname.
                $host->setVerification('pending');
            }
        }

        if (null !== $input->surface) {
            $host->setSurface(SurfaceType::from($input->surface));
        }

        if (null !== $input->enabled) {
            $host->setIsEnabled($input->enabled);
        }

        if ($input->siteIdProvided) {
            if (null === $input->siteId) {
                $result = $this->unassigner->unassign($host);
                $this->assertAdminSurfaceLegal($result->host);

                return $result;
            }

            $currentSiteId = $host->getSite()?->getId();
            if ($currentSiteId === $input->siteId) {
                $this->assertAdminSurfaceLegal($host);
                try {
                    $this->em->flush();
                } catch (UniqueConstraintViolationException $e) {
                    throw new HostHostTakenException(previous: $e);
                }

                return new HostMutationResult(
                    $host,
                    $this->accessModeResetter->resetToPathIfNeeded(),
                );
            }

            $updated = $this->assigner->assign($host, $input->siteId);
            $this->assertAdminSurfaceLegal($updated);

            return new HostMutationResult(
                $updated,
                $this->accessModeResetter->resetToPathIfNeeded(),
            );
        }

        $this->assertAdminSurfaceLegal($host);
        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new HostHostTakenException(previous: $e);
        }

        return new HostMutationResult(
            $host,
            $this->accessModeResetter->resetToPathIfNeeded(),
        );
    }

    /**
     * @throws HostProtectedException
     */
    private function assertProtectedMutable(SiteHost $host, UpdateHostInput $input): void
    {
        if (!$host->isProtected()) {
            return;
        }

        if (null !== $input->surface && SurfaceType::Site->value !== $input->surface) {
            throw new HostProtectedException('Protected system host must keep the site surface.');
        }

        if (null !== $input->enabled && false === $input->enabled) {
            throw new HostProtectedException('Protected system host cannot be disabled.');
        }

        if ($input->siteIdProvided) {
            $currentSiteId = $host->getSite()?->getId();
            if (null === $input->siteId || $input->siteId !== $currentSiteId) {
                throw new HostProtectedException('Protected system host cannot leave the Main site.');
            }
        }
    }

    /**
     * @throws HostAdminSurfaceNotAllowedException
     */
    private function assertAdminSurfaceLegal(SiteHost $host): void
    {
        if (SurfaceType::Admin !== $host->getSurface()) {
            return;
        }

        $existing = $this->hosts->findAdminSurfaceHost();
        if (HostAdminSurfaceRules::canClaimAdminSurface($host->getSite(), $host->getId(), $existing)) {
            return;
        }

        $message = HostAdminSurfaceRules::allowsAdminSurface($host->getSite())
            ? 'An admin surface host already exists.'
            : 'Admin surface is only allowed on the Main site.';

        throw new HostAdminSurfaceNotAllowedException($message);
    }
}
