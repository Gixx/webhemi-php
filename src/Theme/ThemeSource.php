<?php

declare(strict_types=1);

namespace App\Theme;

/**
 * Where a resolved frontend theme package lives on disk.
 */
enum ThemeSource: string
{
    case Shipped = 'shipped';
    case Uploaded = 'uploaded';
}
