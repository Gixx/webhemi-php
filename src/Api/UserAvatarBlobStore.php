<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Stores user avatar JPEGs under var/avatars/{ab}/{hash}.
 */
final class UserAvatarBlobStore
{
    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    /**
     * @return array{contentHash: string, storageKey: string, byteSize: int}
     */
    public function storeFromPath(string $absolutePath): array
    {
        if (!is_file($absolutePath) || !is_readable($absolutePath)) {
            throw new \InvalidArgumentException('Uploaded avatar is not readable.');
        }

        $hash = hash_file('sha256', $absolutePath);
        if (false === $hash) {
            throw new \RuntimeException('Could not hash avatar file.');
        }

        $byteSize = filesize($absolutePath);
        if (false === $byteSize) {
            throw new \RuntimeException('Could not read avatar file size.');
        }

        $prefix = substr($hash, 0, 2);
        $dir = $this->projectDir . '/var/avatars/' . $prefix;
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('Could not create avatar storage directory.');
        }

        $storageKey = $prefix . '/' . $hash;
        $target = $this->absolutePath($storageKey);
        if (!is_file($target) && !copy($absolutePath, $target)) {
            throw new \RuntimeException('Could not store avatar blob.');
        }

        return [
            'contentHash' => $hash,
            'storageKey' => $storageKey,
            'byteSize' => $byteSize,
        ];
    }

    public function absolutePath(string $storageKey): string
    {
        return $this->projectDir . '/var/avatars/' . ltrim($storageKey, '/');
    }

    public function deleteIfExists(?string $storageKey): void
    {
        if (null === $storageKey || '' === $storageKey) {
            return;
        }
        $path = $this->absolutePath($storageKey);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
