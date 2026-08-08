<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<SiteHost> */
class SiteHostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteHost::class);
    }

    public function findOneByHost(string $host): ?SiteHost
    {
        return $this->findOneBy(['host' => strtolower(trim($host)), 'isEnabled' => true]);
    }

    /**
     * Primary site-surface host for the Main site (enabled), preferred www.* then lowest id.
     */
    public function findMainSiteHost(): ?SiteHost
    {
        /** @var list<SiteHost> $hosts */
        $hosts = $this->createQueryBuilder('h')
            ->innerJoin('h.site', 's')
            ->andWhere('s.slug = :slug')
            ->andWhere('h.surface = :surface')
            ->andWhere('h.isEnabled = true')
            ->setParameter('slug', Site::MAIN_SLUG)
            ->setParameter('surface', SurfaceType::Site)
            ->orderBy('h.id', 'ASC')
            ->getQuery()
            ->getResult();

        if ([] === $hosts) {
            return null;
        }

        foreach ($hosts as $host) {
            if (str_starts_with($host->getHost(), 'www.')) {
                return $host;
            }
        }

        return $hosts[0];
    }

    /**
     * Healthy admin-surface host for the Main site (verified + enabled).
     */
    public function findMainAdminHost(): ?SiteHost
    {
        return $this->createQueryBuilder('h')
            ->innerJoin('h.site', 's')
            ->andWhere('s.slug = :slug')
            ->andWhere('h.surface = :surface')
            ->andWhere('h.verification = :verification')
            ->andWhere('h.isEnabled = true')
            ->setParameter('slug', Site::MAIN_SLUG)
            ->setParameter('surface', SurfaceType::Admin)
            ->setParameter('verification', 'verified')
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Any host currently marked surface=admin (at most one expected).
     */
    public function findAdminSurfaceHost(): ?SiteHost
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.surface = :surface')
            ->setParameter('surface', SurfaceType::Admin)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
