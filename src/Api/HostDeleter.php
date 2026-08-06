<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\SiteHost;
use Doctrine\ORM\EntityManagerInterface;

final class HostDeleter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function delete(SiteHost $host): void
    {
        $this->em->remove($host);
        $this->em->flush();
    }
}
