<?php

declare(strict_types=1);

namespace App\Api;

final class RoleProtectedException extends \RuntimeException
{
    public function __construct(
        string $message = 'Protected system role cannot be modified.',
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
