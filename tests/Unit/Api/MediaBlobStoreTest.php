<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\MediaBlobStore;
use PHPUnit\Framework\TestCase;

final class MediaBlobStoreTest extends TestCase
{
    public function testStoresAndHashesFile(): void
    {
        $root = sys_get_temp_dir() . '/webhemi-media-' . uniqid('', true);
        mkdir($root);
        $src = $root . '/in.txt';
        file_put_contents($src, 'hello-webhemi');

        try {
            $store = new MediaBlobStore($root);
            $result = $store->storeFromPath($src, 'hello.txt', 'text/plain');

            self::assertSame(hash_file('sha256', $src), $result['contentHash']);
            self::assertSame(13, $result['byteSize']);
            self::assertSame('hello.txt', $result['originalFilename']);
            self::assertFileExists($store->absolutePath($result['storageKey']));
        } finally {
            $this->rmTree($root);
        }
    }

    private function rmTree(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) ?: [] as $item) {
            if ('.' === $item || '..' === $item) {
                continue;
            }
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->rmTree($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}
