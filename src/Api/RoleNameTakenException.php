<?php

declare(strict_types=1);

namespace App\Api;

final class RoleNameTakenException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('A role with this name already exists.', 0, $previous);
    }
}
