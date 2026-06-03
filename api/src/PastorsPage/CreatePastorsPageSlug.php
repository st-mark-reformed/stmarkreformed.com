<?php

declare(strict_types=1);

namespace App\PastorsPage;

use Cocur\Slugify\Slugify;

readonly class CreatePastorsPageSlug
{
    public static function create(
        NewPastorsPageItem|PastorsPageItem $pastorsPageItem,
    ): string {
        return new Slugify()->slugify($pastorsPageItem->title);
    }
}
