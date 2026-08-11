<?php

declare(strict_types=1);

namespace App\Api;

final class UserEmailTakenException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('Email is already taken.', 0, $previous);
    }
}
