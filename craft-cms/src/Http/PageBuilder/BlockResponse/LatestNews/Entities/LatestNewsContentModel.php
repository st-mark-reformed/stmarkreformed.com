<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\LatestNews\Entities;

use function array_map;

class LatestNewsContentModel
{
    /** @var NewsItem[] */
    private array $newsItems;

    /**
     * @param NewsItem[] $newsItems
     */
    public function __construct(
        private string $heading,
        private string $subHeading,
        array $newsItems,
    ) {
        array_map(
            [$this, 'addNewsItem'],
            $newsItems,
        );
    }

    private function addNewsItem(NewsItem $newsItem): void
    {
        $this->newsItems[] = $newsItem;
    }

    public function heading(): string
    {
        return $this->heading;
    }

    public function subHeading(): string
    {
        return $this->subHeading;
    }

    public function hasHeadings(): bool
    {
        return $this->heading !== '' || $this->subHeading !== '';
    }

    /**
     * @return NewsItem[]
     */
    public function newsItems(): array
    {
        return $this->newsItems;
    }
}
