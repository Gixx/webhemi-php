<?php

declare(strict_types=1);

namespace App\Entity;

enum ContentNodeKind: string
{
    case Folder = 'folder';
    case Document = 'document';
    case MediaRef = 'media_ref';
    case Redirect = 'redirect';
}
