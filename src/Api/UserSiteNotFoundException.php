<?php

declare(strict_types=1);

namespace App\Api;

final class UserSiteNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('One or more sites were not found.');
    }
}
