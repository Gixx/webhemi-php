<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Walks Lexical SerializedEditorState JSON into a safe HTML fragment.
 */
final class LexicalHtmlRenderer
{
    public function render(?string $bodyJson): string
    {
        if (null === $bodyJson || '' === trim($bodyJson)) {
            return '';
        }

        try {
            $decoded = json_decode($bodyJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return '';
        }

        if (!\is_array($decoded) || !isset($decoded['root']) || !\is_array($decoded['root'])) {
            return '';
        }

        $children = $decoded['root']['children'] ?? [];
        if (!\is_array($children)) {
            return '';
        }

        $html = '';
        foreach ($children as $child) {
            if (\is_array($child)) {
                $html .= $this->renderNode($child);
            }
        }

        return $html;
    }

    /**
     * @param array<string, mixed> $node
     */
    private function renderNode(array $node): string
    {
        $type = isset($node['type']) ? (string) $node['type'] : '';

        return match ($type) {
            'paragraph' => $this->wrapBlock('p', $this->renderInlineChildren($node)),
            'heading' => $this->renderHeading($node),
            'list' => $this->renderList($node),
            'quote' => $this->wrapBlock('blockquote', $this->renderInlineChildren($node)),
            'wh-accordion' => $this->renderAccordion($node),
            'link' => $this->renderLink($node),
            'text' => $this->renderText($node),
            'linebreak' => '<br>',
            default => $this->renderUnknownBlock($node),
        };
    }

    /**
     * @param array<string, mixed> $node
     */
    private function renderHeading(array $node): string
    {
        $tag = isset($node['tag']) ? strtolower((string) $node['tag']) : 'h2';
        if (!\in_array($tag, ['h2', 'h3'], true)) {
            $tag = 'h2';
        }

        return $this->wrapBlock($tag, $this->renderInlineChildren($node));
    }

    /**
     * @param array<string, mixed> $node
     */
    private function renderList(array $node): string
    {
        $listType = isset($node['listType']) ? (string) $node['listType'] : 'bullet';
        $tag = 'number' === $listType ? 'ol' : 'ul';
        $items = '';
        foreach ($node['children'] ?? [] as $child) {
            if (!\is_array($child)) {
                continue;
            }
            if (($child['type'] ?? '') === 'listitem') {
                $items .= $this->wrapBlock('li', $this->renderInlineChildren($child));
            }
        }

        return $this->wrapBlock($tag, $items);
    }

    /**
     * @param array<string, mixed> $node
     */
    private function renderAccordion(array $node): string
    {
        $blockId = isset($node['blockId']) ? (string) $node['blockId'] : '';
        $items = $node['items'] ?? [];
        if (!\is_array($items) || [] === $items) {
            return '';
        }

        $inner = '';
        foreach ($items as $item) {
            if (!\is_array($item)) {
                continue;
            }
            $title = isset($item['title']) ? (string) $item['title'] : '';
            $body = isset($item['body']) ? (string) $item['body'] : '';
            $inner .= '<details class="wh-accordion__item">'
                . '<summary class="wh-accordion__title">' . $this->esc($title) . '</summary>'
                . '<div class="wh-accordion__body">' . nl2br($this->esc($body), false) . '</div>'
                . '</details>';
        }

        $idAttr = '' !== $blockId ? ' id="' . $this->esc($blockId) . '"' : '';

        return '<div class="wh-accordion"' . $idAttr . ' data-wh-block="accordion">' . $inner . '</div>';
    }

    /**
     * @param array<string, mixed> $node
     */
    private function renderInlineChildren(array $node): string
    {
        $html = '';
        foreach ($node['children'] ?? [] as $child) {
            if (!\is_array($child)) {
                continue;
            }
            $type = isset($child['type']) ? (string) $child['type'] : '';
            $html .= match ($type) {
                'text' => $this->renderText($child),
                'link' => $this->renderLink($child),
                'linebreak' => '<br>',
                'paragraph', 'heading', 'list', 'listitem', 'quote', 'wh-accordion' => $this->renderNode($child),
                default => $this->renderInlineChildren($child),
            };
        }

        return $html;
    }

    /**
     * @param array<string, mixed> $node
     */
    private function renderText(array $node): string
    {
        $text = isset($node['text']) ? (string) $node['text'] : '';
        $html = $this->esc($text);
        $format = (int) ($node['format'] ?? 0);
        // Lexical bit flags: 1 = bold, 2 = italic
        if (($format & 1) !== 0) {
            $html = '<strong>' . $html . '</strong>';
        }
        if (($format & 2) !== 0) {
            $html = '<em>' . $html . '</em>';
        }

        return $html;
    }

    /**
     * @param array<string, mixed> $node
     */
    private function renderLink(array $node): string
    {
        $url = isset($node['url']) ? (string) $node['url'] : '';
        $safe = $this->sanitizeUrl($url);
        $inner = $this->renderInlineChildren($node);
        if (null === $safe) {
            return $inner;
        }

        return '<a href="' . $this->esc($safe) . '">' . $inner . '</a>';
    }

    /**
     * @param array<string, mixed> $node
     */
    private function renderUnknownBlock(array $node): string
    {
        if (isset($node['children']) && \is_array($node['children'])) {
            return $this->renderInlineChildren($node);
        }

        return '';
    }

    private function wrapBlock(string $tag, string $inner): string
    {
        return '<' . $tag . '>' . $inner . '</' . $tag . '>';
    }

    private function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function sanitizeUrl(string $url): ?string
    {
        $url = trim($url);
        if ('' === $url) {
            return null;
        }
        if (str_starts_with($url, '/')) {
            return $url;
        }
        $lower = strtolower($url);
        foreach (['https://', 'http://', 'mailto:'] as $prefix) {
            if (str_starts_with($lower, $prefix)) {
                return $url;
            }
        }

        return null;
    }
}
