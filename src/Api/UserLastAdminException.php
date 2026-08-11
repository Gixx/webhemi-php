<?php

declare(strict_types=1);

namespace App\Api;

final class UserLastAdminException extends \RuntimeException
{
    public function __construct(string $message = 'Cannot remove the last Administrator.')
    {
        parent::__construct($message);
    }
}
