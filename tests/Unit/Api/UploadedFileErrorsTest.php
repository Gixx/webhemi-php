<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\UploadedFileErrors;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedFileErrorsTest extends TestCase
{
    public function testIniSizeMentionsUploadLimit(): void
    {
        $file = new UploadedFile(
            __FILE__,
            'clip.mp4',
            'video/mp4',
            \UPLOAD_ERR_INI_SIZE,
            true,
        );

        $message = UploadedFileErrors::message($file);

        self::assertStringContainsString('maximum upload size', $message);
        self::assertStringContainsString((string) ini_get('upload_max_filesize'), $message);
    }

    public function testPartialUploadMessage(): void
    {
        $file = new UploadedFile(
            __FILE__,
            'clip.mp4',
            'video/mp4',
            \UPLOAD_ERR_PARTIAL,
            true,
        );

        self::assertSame('The file was only partially uploaded.', UploadedFileErrors::message($file));
    }
}
