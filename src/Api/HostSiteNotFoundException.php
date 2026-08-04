<?php

declare(strict_types=1);

namespace App\Api;

final class HostSiteNotFoundException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('The selected site does not exist.', 0, $previous);
    }
}
