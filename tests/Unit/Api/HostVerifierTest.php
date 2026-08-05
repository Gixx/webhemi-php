<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\HostNotPendingException;
use App\Api\HostVerificationFailedException;
use App\Api\HostVerifier;
use App\Entity\SiteHost;
use App\SiteHost\Verification\HostOwnershipProbe;
use App\SiteHost\Verification\HostVerificationResult;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class HostVerifierTest extends TestCase
{
    public function testVerifySetsVerifiedOnSuccess(): void
    {
        $host = (new SiteHost())->setHost('pending.example.test')->setStatus('pending');

        $probe = new class () implements HostOwnershipProbe {
            public function verify(string $host): HostVerificationResult
            {
                return new HostVerificationResult(true, 'http://pending.example.test/abc.txt');
            }
        };

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        $updated = (new HostVerifier($probe, $em))->verify($host);

        self::assertSame('verified', $updated->getStatus());
    }

    public function testVerifyRejectsNonPending(): void
    {
        $host = (new SiteHost())->setHost('done.example.test')->setStatus('verified');

        $probe = new class () implements HostOwnershipProbe {
            public function verify(string $host): HostVerificationResult
            {
                self::fail('Probe must not run for non-pending hosts.');
            }
        };

        $em = $this->createStub(EntityManagerInterface::class);

        $this->expectException(HostNotPendingException::class);
        (new HostVerifier($probe, $em))->verify($host);
    }

    public function testVerifyLeavesPendingOnProbeFailure(): void
    {
        $host = (new SiteHost())->setHost('fail.example.test')->setStatus('pending');

        $probe = new class () implements HostOwnershipProbe {
            public function verify(string $host): HostVerificationResult
            {
                return new HostVerificationResult(false);
            }
        };

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        try {
            (new HostVerifier($probe, $em))->verify($host);
            self::fail('Expected HostVerificationFailedException');
        } catch (HostVerificationFailedException) {
            self::assertSame('pending', $host->getStatus());
        }
    }
}
