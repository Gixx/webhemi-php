<?php

declare(strict_types=1);

namespace App\Api;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/** Human-readable messages for failed PHP multipart uploads. */
final class UploadedFileErrors
{
    public static function message(UploadedFile $file): string
    {
        return match ($file->getError()) {
            \UPLOAD_ERR_INI_SIZE, \UPLOAD_ERR_FORM_SIZE => sprintf(
                'File exceeds the maximum upload size (%s).',
                ini_get('upload_max_filesize') ?: 'unknown',
            ),
            \UPLOAD_ERR_PARTIAL => 'The file was only partially uploaded.',
            \UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            \UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder for uploads.',
            \UPLOAD_ERR_CANT_WRITE => 'Failed to write uploaded file to disk.',
            \UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
            default => $file->getErrorMessage() ?: 'Uploaded file is invalid.',
        };
    }
}
