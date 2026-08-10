<?php

declare(strict_types=1);

namespace App\Api;

final class PermissionHasRolesException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('Permission is still assigned to one or more roles.', 0, $previous);
    }
}
