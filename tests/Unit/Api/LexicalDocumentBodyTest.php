<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\LexicalDocumentBody;
use PHPUnit\Framework\TestCase;

final class LexicalDocumentBodyTest extends TestCase
{
    public function testEmptyIsValid(): void
    {
        self::assertTrue(LexicalDocumentBody::isValid(null));
        self::assertTrue(LexicalDocumentBody::isValid(''));
    }

    public function testLexicalRootObjectIsValid(): void
    {
        $json = '{"root":{"children":[],"direction":null,"format":"","indent":0,"type":"root","version":1}}';
        self::assertTrue(LexicalDocumentBody::isValid($json));
    }

    public function testInvalidJsonRejected(): void
    {
        self::assertFalse(LexicalDocumentBody::isValid('{not-json'));
        self::assertFalse(LexicalDocumentBody::isValid('[]'));
        self::assertFalse(LexicalDocumentBody::isValid('{"foo":1}'));
        self::assertFalse(LexicalDocumentBody::isValid('<p>legacy</p>'));
    }
}
