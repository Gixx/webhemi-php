<?php

declare(strict_types=1);

namespace App\Api;

final class UserSelfDeleteException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('You cannot delete your own account.');
    }
}
