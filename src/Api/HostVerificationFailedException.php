<?php

declare(strict_types=1);

namespace App\Api;

final class HostVerificationFailedException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('Host ownership verification failed.', 0, $previous);
    }
}
