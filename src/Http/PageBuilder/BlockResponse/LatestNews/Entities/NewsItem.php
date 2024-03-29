<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\LatestNews\Entities;

class NewsItem
{
    public function __construct(
        private string $title,
        private string $excerpt,
        private string $url,
    ) {
    }

    public function title(): string
    {
        return $this->title;
    }

    public function excerpt(): string
    {
        return $this->excerpt;
    }

    public function url(): string
    {
        return $this->url;
    }
}
