<?php

declare(strict_types=1);

namespace App\Api;

final class HostNotVerifiedForAssignException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('Only verified, unassigned hosts can be assigned to a site.', 0, $previous);
    }
}
