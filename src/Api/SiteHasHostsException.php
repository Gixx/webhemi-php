<?php

declare(strict_types=1);

namespace App\Api;

final class SiteHasHostsException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('Site still has assigned hosts.', 0, $previous);
    }
}
