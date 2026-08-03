<?php

declare(strict_types=1);

namespace App\Api;

final class SiteSlugTakenException extends \RuntimeException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('A site with this slug already exists.', 0, $previous);
    }
}
