<?php

declare(strict_types=1);

namespace App\Api;

final class RoleHasUsersException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('Role is still assigned to one or more users.', 0, $previous);
    }
}
