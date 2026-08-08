<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;

final class SiteDeleter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws SiteHasHostsException
     * @throws SiteProtectedException
     */
    public function delete(Site $site): void
    {
        if ($site->isProtected()) {
            throw new SiteProtectedException('Protected system site cannot be deleted.');
        }

        if (!$site->getHosts()->isEmpty()) {
            throw new SiteHasHostsException();
        }

        $this->em->remove($site);
        $this->em->flush();
    }
}
