<?php

declare(strict_types=1);

namespace App\Content;

use App\Entity\ContentNode;
use App\Entity\ContentNodeKind;

/**
 * Result of resolving a public CMS path.
 */
final class PublicContentHit
{
    private function __construct(
        public readonly ?ContentNode $node,
        public readonly string $path,
        public readonly bool $isRootIndex,
    ) {
    }

    public static function rootIndex(): self
    {
        return new self(null, '/', true);
    }

    public static function folder(ContentNode $node, string $path): self
    {
        return new self($node, $path, false);
    }

    public static function leaf(ContentNode $node, string $path): self
    {
        return new self($node, $path, false);
    }

    public function isFolderIndex(): bool
    {
        return $this->isRootIndex || (
            $this->node instanceof ContentNode
            && ContentNodeKind::Folder === $this->node->getKind()
        );
    }
}
