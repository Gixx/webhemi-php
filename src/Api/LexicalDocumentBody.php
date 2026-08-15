<?php

declare(strict_types=1);

namespace App\Api;

/**
 * Validates Lexical SerializedEditorState JSON stored in content_node.body.
 */
final class LexicalDocumentBody
{
    public static function isValid(?string $body): bool
    {
        if (null === $body || '' === $body) {
            return true;
        }

        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return false;
        }

        return \is_array($decoded)
            && isset($decoded['root'])
            && \is_array($decoded['root']);
    }
}
