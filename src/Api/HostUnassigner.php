<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;
use App\Entity\SiteHost;
use Doctrine\ORM\EntityManagerInterface;

final class HostUnassigner
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Detach host from its site without deleting the row.
     * Active hosts become verified so they can be assigned again later.
     */
    public function unassign(SiteHost $host): SiteHost
    {
        if (!$host->getSite() instanceof Site) {
            return $host;
        }

        if ('active' === $host->getStatus()) {
            $host->setStatus('verified');
        }

        $host->setSite(null);
        $this->em->flush();

        return $host;
    }
}
