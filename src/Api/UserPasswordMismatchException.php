<?php

declare(strict_types=1);

namespace App\Api;

final class UserPasswordMismatchException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Current password is incorrect.');
    }
}
