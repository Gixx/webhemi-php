<?php

declare(strict_types=1);

namespace App\Routing;

final class HostContextHolder
{
    private ?HostContext $context = null;

    public function set(HostContext $context): void
    {
        $this->context = $context;
    }

    public function get(): HostContext
    {
        return $this->context ?? new HostContext(null);
    }
}
