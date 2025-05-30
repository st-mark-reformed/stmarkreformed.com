<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsList;

class NewsItem
{
    public function __construct(
        private string $title,
        private string $slug,
        private string $excerpt,
        private string $content,
        private string $url,
        private string $readableDate,
    ) {
    }

    public function title(): string
    {
        return $this->title;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function excerpt(): string
    {
        return $this->excerpt;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function readableDate(): string
    {
        return $this->readableDate;
    }
}
