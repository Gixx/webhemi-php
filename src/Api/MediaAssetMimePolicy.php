<?php

declare(strict_types=1);

namespace App\Api;

/** Allowed MIME / extensions for Media Library uploads. */
final class MediaAssetMimePolicy
{
    /** @var list<string> */
    private const OFFICE_MIME = [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.presentation',
        'application/rtf',
        'text/rtf',
    ];

    /** @var list<string> */
    private const EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tif', 'tiff', 'ico', 'avif',
        'mp4', 'webm', 'mov', 'avi', 'mkv', 'm4v', 'ogv',
        'mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'oga',
        'pdf',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'rtf',
    ];

    public static function isAllowed(?string $mimeType, string $originalFilename): bool
    {
        $mime = strtolower(trim((string) $mimeType));
        if (
            str_starts_with($mime, 'image/')
            || str_starts_with($mime, 'video/')
            || str_starts_with($mime, 'audio/')
            || 'application/pdf' === $mime
            || \in_array($mime, self::OFFICE_MIME, true)
        ) {
            return true;
        }

        $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));

        return '' !== $ext && \in_array($ext, self::EXTENSIONS, true);
    }

    public static function rejectionMessage(): string
    {
        return 'Only images, video, audio, PDF, and Office documents are allowed.';
    }
}
