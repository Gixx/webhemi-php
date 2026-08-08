<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\SiteHost;
use Doctrine\ORM\EntityManagerInterface;

final class HostDeleter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdminAccessModeResetter $accessModeResetter,
    ) {
    }

    /**
     * @return bool True when access.admin was forced back to path.
     *
     * @throws HostProtectedException
     */
    public function delete(SiteHost $host): bool
    {
        if ($host->isProtected()) {
            throw new HostProtectedException('Protected system host cannot be deleted.');
        }

        $this->em->remove($host);
        $this->em->flush();

        return $this->accessModeResetter->resetToPathIfNeeded();
    }
}
