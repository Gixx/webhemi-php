<?php

declare(strict_types=1);

namespace App\Entity;

enum ContentTree: string
{
    case Site = 'site';
    case Media = 'media';
}
