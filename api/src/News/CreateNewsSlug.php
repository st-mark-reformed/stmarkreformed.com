<?php

declare(strict_types=1);

namespace App\News;

use Cocur\Slugify\Slugify;

readonly class CreateNewsSlug
{
    public static function create(NewNewsItem|NewsItem $newsItem): string
    {
        return new Slugify()->slugify($newsItem->title);
    }
}
