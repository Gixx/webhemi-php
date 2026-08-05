<?php

declare(strict_types=1);

namespace App\Api;

final class HostAlreadyAssignedException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('Host is already assigned to a site.', 0, $previous);
    }
}
