<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\MediaAssetMimePolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MediaAssetMimePolicyTest extends TestCase
{
    #[DataProvider('allowedProvider')]
    public function testAllowsExpectedTypes(?string $mime, string $filename): void
    {
        self::assertTrue(MediaAssetMimePolicy::isAllowed($mime, $filename));
    }

    #[DataProvider('rejectedProvider')]
    public function testRejectsUnexpectedTypes(?string $mime, string $filename): void
    {
        self::assertFalse(MediaAssetMimePolicy::isAllowed($mime, $filename));
    }

    /**
     * @return iterable<string, array{0: ?string, 1: string}>
     */
    public static function allowedProvider(): iterable
    {
        yield 'jpeg' => ['image/jpeg', 'photo.jpg'];
        yield 'mp4' => ['video/mp4', 'clip.mp4'];
        yield 'mp3' => ['audio/mpeg', 'track.mp3'];
        yield 'pdf' => ['application/pdf', 'doc.pdf'];
        yield 'docx mime' => [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'brief.docx',
        ];
        yield 'xlsx by extension' => ['', 'sheet.xlsx'];
        yield 'png empty mime' => [null, 'logo.png'];
    }

    /**
     * @return iterable<string, array{0: ?string, 1: string}>
     */
    public static function rejectedProvider(): iterable
    {
        yield 'exe' => ['application/x-msdownload', 'virus.exe'];
        yield 'js' => ['text/javascript', 'hack.js'];
        yield 'zip' => ['application/zip', 'archive.zip'];
        yield 'unknown empty' => ['', 'readme'];
    }
}
