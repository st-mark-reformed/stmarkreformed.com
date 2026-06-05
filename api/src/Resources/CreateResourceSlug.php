<?php

declare(strict_types=1);

namespace App\Resources;

use Cocur\Slugify\Slugify;

readonly class CreateResourceSlug
{
    public static function create(NewResourceItem|ResourceItem $resourceItem): string
    {
        return new Slugify()->slugify($resourceItem->title);
    }
}
