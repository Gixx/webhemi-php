<?php

declare(strict_types=1);

namespace App\Api;

final class HostNotPendingException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('Only pending hosts can be verified.', 0, $previous);
    }
}
