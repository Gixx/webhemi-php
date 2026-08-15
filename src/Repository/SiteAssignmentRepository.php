<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Site;
use App\Entity\SiteAssignment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<SiteAssignment> */
class SiteAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteAssignment::class);
    }

    public function findForUserAndSite(User $user, int $siteId): ?SiteAssignment
    {
        return $this->findOneBy(['user' => $user, 'site' => $siteId]);
    }

    /**
     * @return list<SiteAssignment>
     */
    public function findBySite(Site $site): array
    {
        /** @var list<SiteAssignment> */
        return $this->createQueryBuilder('a')
            ->andWhere('a.site = :site')
            ->setParameter('site', $site)
            ->leftJoin('a.user', 'u')
            ->addSelect('u')
            ->leftJoin('a.role', 'r')
            ->addSelect('r')
            ->orderBy('u.email', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
