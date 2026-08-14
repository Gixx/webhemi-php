<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Stores uploaded media blobs under var/media/{ab}/{hash}.
 */
final class MediaBlobStore
{
    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    /**
     * @return array{contentHash: string, storageKey: string, byteSize: int, mimeType: string, originalFilename: string}
     */
    public function storeFromPath(
        string $absolutePath,
        string $originalFilename,
        ?string $mimeType = null,
    ): array {
        if (!is_file($absolutePath) || !is_readable($absolutePath)) {
            throw new \InvalidArgumentException('Uploaded file is not readable.');
        }

        $hash = hash_file('sha256', $absolutePath);
        if (false === $hash) {
            throw new \RuntimeException('Could not hash uploaded file.');
        }

        $byteSize = filesize($absolutePath);
        if (false === $byteSize) {
            throw new \RuntimeException('Could not read uploaded file size.');
        }

        $prefix = substr($hash, 0, 2);
        $dir = $this->projectDir . '/var/media/' . $prefix;
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('Could not create media storage directory.');
        }

        $storageKey = $prefix . '/' . $hash;
        $target = $this->projectDir . '/var/media/' . $storageKey;
        if (!is_file($target) && !copy($absolutePath, $target)) {
            throw new \RuntimeException('Could not store uploaded media blob.');
        }

        $detected = $mimeType;
        if (null === $detected || '' === trim($detected)) {
            $detected = mime_content_type($absolutePath) ?: 'application/octet-stream';
        }

        return [
            'contentHash' => $hash,
            'storageKey' => $storageKey,
            'byteSize' => $byteSize,
            'mimeType' => $detected,
            'originalFilename' => basename($originalFilename),
        ];
    }

    public function absolutePath(string $storageKey): string
    {
        return $this->projectDir . '/var/media/' . ltrim($storageKey, '/');
    }

    public function deleteIfExists(string $storageKey): void
    {
        $path = $this->absolutePath($storageKey);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
