<?php

declare(strict_types=1);

namespace App\Api;

final class HostAdminSurfaceNotAllowedException extends \RuntimeException
{
    public function __construct(
        string $message = 'Admin surface is only allowed on the Main site.',
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
