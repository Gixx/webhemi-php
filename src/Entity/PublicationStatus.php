<?php

declare(strict_types=1);

namespace App\Entity;

enum PublicationStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Scheduled = 'scheduled';
}
