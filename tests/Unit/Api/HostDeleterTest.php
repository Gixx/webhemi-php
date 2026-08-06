<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\HostDeleter;
use App\Entity\SiteHost;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class HostDeleterTest extends TestCase
{
    public function testDeletesHost(): void
    {
        $host = (new SiteHost())->setHost('gone.example.test');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('remove')->with($host);
        $em->expects(self::once())->method('flush');

        (new HostDeleter($em))->delete($host);
    }
}
