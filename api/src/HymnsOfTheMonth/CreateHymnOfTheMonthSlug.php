<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth;

use Cocur\Slugify\Slugify;

/**
 * Derives the slug from the (already-derived) month title, e.g.
 * "January, 2024" => "january-2024". This reproduces the slug Craft generated
 * from its `{date|date('F, Y')}` title format, so existing permalinks hold.
 */
readonly class CreateHymnOfTheMonthSlug
{
    public static function create(
        NewHymnOfTheMonthItem|HymnOfTheMonthItem $hymnOfTheMonthItem,
    ): string {
        return new Slugify()->slugify($hymnOfTheMonthItem->title);
    }
}
