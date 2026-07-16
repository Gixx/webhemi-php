<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SiteHost;
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
        return $this->findOneBy(['host' => strtolower(trim($host)), 'isActive' => true]);
    }
}
