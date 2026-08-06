<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Repository\SiteHostRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class HostCreator
{
    public function __construct(
        private readonly SiteHostRepository $hosts,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws HostHostTakenException
     */
    public function create(CreateHostInput $input): SiteHost
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('CreateHostInput must be valid before create().');
        }

        if ($this->hosts->findOneBy(['host' => $input->host]) instanceof SiteHost) {
            throw new HostHostTakenException();
        }

        $host = (new SiteHost())
            ->setSite(null)
            ->setHost($input->host)
            ->setSurface(SurfaceType::from($input->surface))
            ->setVerification('pending')
            ->setIsEnabled($input->enabled);

        $this->em->persist($host);

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new HostHostTakenException(previous: $e);
        }

        return $host;
    }
}
