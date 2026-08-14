<?php

declare(strict_types=1);

namespace App\Api;

final class ContentNodeSlugTakenException extends \RuntimeException
{
    public function __construct(
        string $message = 'A node with this slug already exists under the same parent.',
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
