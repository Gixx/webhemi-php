<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ContentNode;
use App\Entity\ContentTree;
use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContentNode>
 */
class ContentNodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentNode::class);
    }

    public function findLiveSiblingSlug(
        Site $site,
        ContentTree $tree,
        ?ContentNode $parent,
        string $slug,
        ?int $excludeId = null,
    ): ?ContentNode {
        $qb = $this->createQueryBuilder('n')
            ->andWhere('n.site = :site')
            ->andWhere('n.tree = :tree')
            ->andWhere('n.slug = :slug')
            ->andWhere('n.deletedAt IS NULL')
            ->setParameter('site', $site)
            ->setParameter('tree', $tree)
            ->setParameter('slug', $slug);

        if ($parent instanceof ContentNode) {
            $qb->andWhere('n.parent = :parent')->setParameter('parent', $parent);
        } else {
            $qb->andWhere('n.parent IS NULL');
        }

        if (null !== $excludeId) {
            $qb->andWhere('n.id != :excludeId')->setParameter('excludeId', $excludeId);
        }

        return $qb->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    /**
     * @return list<ContentNode>
     */
    public function findLiveChildren(Site $site, ContentTree $tree, ?ContentNode $parent): array
    {
        $qb = $this->createQueryBuilder('n')
            ->andWhere('n.site = :site')
            ->andWhere('n.tree = :tree')
            ->andWhere('n.deletedAt IS NULL')
            ->setParameter('site', $site)
            ->setParameter('tree', $tree)
            ->orderBy('n.sortOrder', 'ASC')
            ->addOrderBy('n.title', 'ASC');

        if ($parent instanceof ContentNode) {
            $qb->andWhere('n.parent = :parent')->setParameter('parent', $parent);
        } else {
            $qb->andWhere('n.parent IS NULL');
        }

        /** @var list<ContentNode> */
        return $qb->getQuery()->getResult();
    }

    /**
     * All live nodes in one tree (for explorer forest assembly).
     *
     * @return list<ContentNode>
     */
    public function findLiveByTree(Site $site, ContentTree $tree): array
    {
        /** @var list<ContentNode> */
        return $this->createQueryBuilder('n')
            ->andWhere('n.site = :site')
            ->andWhere('n.tree = :tree')
            ->andWhere('n.deletedAt IS NULL')
            ->setParameter('site', $site)
            ->setParameter('tree', $tree)
            ->orderBy('n.sortOrder', 'ASC')
            ->addOrderBy('n.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<ContentNode>
     */
    public function findTrash(Site $site): array
    {
        /** @var list<ContentNode> */
        return $this->createQueryBuilder('n')
            ->andWhere('n.site = :site')
            ->andWhere('n.deletedAt IS NOT NULL')
            ->setParameter('site', $site)
            ->orderBy('n.deletedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<ContentNode>
     */
    public function findLiveDescendantsInclusive(ContentNode $root): array
    {
        $site = $root->getSite();
        if (!$site instanceof Site) {
            return [];
        }

        /** @var list<ContentNode> $all */
        $all = $this->createQueryBuilder('n')
            ->andWhere('n.site = :site')
            ->andWhere('n.tree = :tree')
            ->andWhere('n.deletedAt IS NULL')
            ->setParameter('site', $site)
            ->setParameter('tree', $root->getTree())
            ->getQuery()
            ->getResult();

        $byParent = [];
        foreach ($all as $node) {
            $pid = $node->getParent()?->getId() ?? 0;
            $byParent[$pid][] = $node;
        }

        $out = [];
        $stack = [$root];
        while ($stack !== []) {
            $current = array_pop($stack);
            $out[] = $current;
            $cid = (int) $current->getId();
            foreach ($byParent[$cid] ?? [] as $child) {
                $stack[] = $child;
            }
        }

        return $out;
    }

    public function countLiveMediaRefs(int $mediaAssetId): int
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('IDENTITY(n.mediaAsset) = :mid')
            ->andWhere('n.deletedAt IS NULL')
            ->setParameter('mid', $mediaAssetId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
