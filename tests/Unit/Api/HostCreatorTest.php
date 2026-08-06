<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\CreateHostInput;
use App\Api\HostCreator;
use App\Api\HostHostTakenException;
use App\Entity\SiteHost;
use App\Repository\SiteHostRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class HostCreatorTest extends TestCase
{
    public function testCreatesHostWithoutSite(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'orphan.example.test',
        ]);
        self::assertTrue($input->isValid());

        $hosts = $this->createStub(SiteHostRepository::class);
        $hosts->method('findOneBy')->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist')->with(self::callback(
            static function (SiteHost $host): bool {
                return null === $host->getSite()
                    && 'pending' === $host->getVerification();
            },
        ));
        $em->expects(self::once())->method('flush');

        $created = (new HostCreator($hosts, $em))->create($input);

        self::assertNull($created->getSite());
    }

    public function testDuplicateHostThrows(): void
    {
        $input = CreateHostInput::fromPayload([
            'host' => 'www.example.test',
        ]);

        $existing = (new SiteHost())->setHost('www.example.test');
        $hosts = $this->createStub(SiteHostRepository::class);
        $hosts->method('findOneBy')->willReturn($existing);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');

        $this->expectException(HostHostTakenException::class);
        (new HostCreator($hosts, $em))->create($input);
    }
}
