<?php

declare(strict_types=1);

namespace App\Api;

final class PermissionNameTakenException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('A permission with this name already exists.', 0, $previous);
    }
}
