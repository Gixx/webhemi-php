<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MediaAsset;
use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MediaAsset>
 */
class MediaAssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaAsset::class);
    }

    public function findBySiteAndHash(Site $site, string $hash): ?MediaAsset
    {
        return $this->findOneBy([
            'site' => $site,
            'contentHash' => strtolower(trim($hash)),
        ]);
    }

    /**
     * @return list<MediaAsset>
     */
    public function findLiveInFolder(Site $site, ?int $folderNodeId): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.site = :site')
            ->andWhere('a.deletedAt IS NULL')
            ->setParameter('site', $site)
            ->orderBy('a.originalFilename', 'ASC');

        if (null === $folderNodeId) {
            $qb->andWhere('a.folderNode IS NULL');
        } else {
            $qb->andWhere('IDENTITY(a.folderNode) = :folderId')
                ->setParameter('folderId', $folderNodeId);
        }

        /** @var list<MediaAsset> */
        return $qb->getQuery()->getResult();
    }

    /**
     * @return list<MediaAsset>
     */
    public function findTrash(Site $site): array
    {
        /** @var list<MediaAsset> */
        return $this->createQueryBuilder('a')
            ->andWhere('a.site = :site')
            ->andWhere('a.deletedAt IS NOT NULL')
            ->setParameter('site', $site)
            ->orderBy('a.deletedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
