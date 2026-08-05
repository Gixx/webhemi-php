<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\SiteHost;
use App\SiteHost\Verification\HostOwnershipProbe;
use Doctrine\ORM\EntityManagerInterface;

final class HostVerifier
{
    public function __construct(
        private readonly HostOwnershipProbe $ownershipProbe,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Run the ownership probe; on success set status to verified.
     *
     * @throws HostNotPendingException
     * @throws HostVerificationFailedException
     */
    public function verify(SiteHost $host): SiteHost
    {
        if ('pending' !== $host->getStatus()) {
            throw new HostNotPendingException();
        }

        $result = $this->ownershipProbe->verify($host->getHost());
        if (!$result->verified) {
            throw new HostVerificationFailedException();
        }

        $host->setStatus('verified');
        $this->em->flush();

        return $host;
    }
}
