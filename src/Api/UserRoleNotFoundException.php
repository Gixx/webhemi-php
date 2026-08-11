<?php

declare(strict_types=1);

namespace App\Api;

final class UserRoleNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('One or more roles were not found.');
    }
}
