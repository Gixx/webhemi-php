<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\SiteApiMapper;
use App\Entity\Site;
use PHPUnit\Framework\TestCase;

final class SiteApiMapperTest extends TestCase
{
    public function testToArray(): void
    {
        $site = (new Site())->setName('Main')->setSlug('main')->setIsEnabled(true);
        $ref = new \ReflectionProperty(Site::class, 'id');
        $ref->setValue($site, 7);

        self::assertSame(
            [
                'id' => 7,
                'slug' => 'main',
                'name' => 'Main',
                'enabled' => true,
                'hostCount' => 0,
            ],
            SiteApiMapper::toArray($site),
        );
    }
}
