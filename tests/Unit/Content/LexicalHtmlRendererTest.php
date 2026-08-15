<?php

declare(strict_types=1);

namespace App\Tests\Unit\Content;

use App\Content\LexicalHtmlRenderer;
use PHPUnit\Framework\TestCase;

final class LexicalHtmlRendererTest extends TestCase
{
    public function testEmptyBody(): void
    {
        $renderer = new LexicalHtmlRenderer();
        self::assertSame('', $renderer->render(null));
        self::assertSame('', $renderer->render(''));
        self::assertSame('', $renderer->render('not-json'));
    }

    public function testParagraphBoldAndLink(): void
    {
        $json = json_encode([
            'root' => [
                'children' => [
                    [
                        'type' => 'paragraph',
                        'children' => [
                            ['type' => 'text', 'text' => 'Hello ', 'format' => 0],
                            ['type' => 'text', 'text' => 'world', 'format' => 1],
                            [
                                'type' => 'link',
                                'url' => 'https://example.com',
                                'children' => [
                                    ['type' => 'text', 'text' => 'link', 'format' => 0],
                                ],
                            ],
                        ],
                    ],
                ],
                'type' => 'root',
            ],
        ], JSON_THROW_ON_ERROR);

        $html = (new LexicalHtmlRenderer())->render($json);
        self::assertStringContainsString('<p>', $html);
        self::assertStringContainsString('<strong>world</strong>', $html);
        self::assertStringContainsString('href="https://example.com"', $html);
        self::assertStringContainsString('>link</a>', $html);
    }

    public function testRejectsJavascriptUrl(): void
    {
        $json = json_encode([
            'root' => [
                'children' => [
                    [
                        'type' => 'paragraph',
                        'children' => [
                            [
                                'type' => 'link',
                                'url' => 'javascript:alert(1)',
                                'children' => [
                                    ['type' => 'text', 'text' => 'x', 'format' => 0],
                                ],
                            ],
                        ],
                    ],
                ],
                'type' => 'root',
            ],
        ], JSON_THROW_ON_ERROR);

        $html = (new LexicalHtmlRenderer())->render($json);
        self::assertStringNotContainsString('javascript:', $html);
        self::assertStringContainsString('x', $html);
    }

    public function testAccordionEscapes(): void
    {
        $json = json_encode([
            'root' => [
                'children' => [
                    [
                        'type' => 'wh-accordion',
                        'blockId' => 'acc-1',
                        'items' => [
                            ['id' => '1', 'title' => '<b>T</b>', 'body' => "A\nB"],
                        ],
                    ],
                ],
                'type' => 'root',
            ],
        ], JSON_THROW_ON_ERROR);

        $html = (new LexicalHtmlRenderer())->render($json);
        self::assertStringContainsString('data-wh-block="accordion"', $html);
        self::assertStringContainsString('id="acc-1"', $html);
        self::assertStringContainsString('&lt;b&gt;T&lt;/b&gt;', $html);
        self::assertStringNotContainsString('<b>T</b>', $html);
    }
}
