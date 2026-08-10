<?php

declare(strict_types=1);

namespace App\Api;

final class RolePermissionNotFoundException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('One or more permissions were not found.', 0, $previous);
    }
}
