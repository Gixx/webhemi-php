<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/** @extends ServiceEntityRepository<User> */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /** Users with global ROLE_ADMIN (via user_role). */
    public function countAdmins(?int $excludeUserId = null): int
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(DISTINCT u.id)')
            ->innerJoin('u.roles', 'r')
            ->andWhere('r.name = :admin')
            ->setParameter('admin', \App\Entity\Role::ADMIN);

        if (null !== $excludeUserId) {
            $qb->andWhere('u.id != :exclude')->setParameter('exclude', $excludeUserId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
