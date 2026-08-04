<?php

declare(strict_types=1);

namespace App\Api;

final class HostHostTakenException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('A host with this hostname already exists.', 0, $previous);
    }
}
