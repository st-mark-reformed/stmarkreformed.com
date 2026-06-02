<?php

declare(strict_types=1);

namespace App\News;

readonly class NewsItemResult
{
    public bool $hasNewsItem;

    public NewsItem $newsItem;

    public function __construct(NewsItem|null $newsItem = null)
    {
        $this->hasNewsItem = $newsItem !== null;
        $this->newsItem    = $newsItem ?? new NewsItem();
    }
}
