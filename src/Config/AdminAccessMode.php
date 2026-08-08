<?php

declare(strict_types=1);

namespace App\Config;

enum AdminAccessMode: string
{
    case Path = 'path';
    case Domain = 'domain';
}
