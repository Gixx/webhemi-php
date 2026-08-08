<?php

declare(strict_types=1);

namespace App\Routing;

interface AdminEntryResolverInterface
{
    public function resolve(): CanonicalAdminEntry;
}
