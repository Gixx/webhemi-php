<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class HostCreator
{
    public function __construct(
        private readonly SiteRepository $sites,
        private readonly SiteHostRepository $hosts,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws HostHostTakenException
     * @throws HostSiteNotFoundException
     */
    public function create(CreateHostInput $input): SiteHost
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('CreateHostInput must be valid before create().');
        }

        $site = null;
        if (null !== $input->siteId) {
            $site = $this->sites->find($input->siteId);
            if (!$site instanceof Site) {
                throw new HostSiteNotFoundException();
            }
        }

        if ($this->hosts->findOneBy(['host' => $input->host]) instanceof SiteHost) {
            throw new HostHostTakenException();
        }

        $host = (new SiteHost())
            ->setSite($site)
            ->setHost($input->host)
            ->setSurface(SurfaceType::from($input->surface))
            ->setStatus('pending')
            ->setIsActive($input->active);

        $this->em->persist($host);

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new HostHostTakenException(previous: $e);
        }

        return $host;
    }
}
