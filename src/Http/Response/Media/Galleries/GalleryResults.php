<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries;

use function array_map;

class GalleryResults
{
    /** @var GalleryItem[] */
    private array $items = [];

    /**
     * @param GalleryItem[] $incomingItems
     */
    public function __construct(
        private bool $hasEntries,
        private int $totalResults,
        array $incomingItems,
    ) {
        array_map(
            function (GalleryItem $item): void {
                $this->items[] = $item;
            },
            $incomingItems,
        );
    }

    public function hasEntries(): bool
    {
        return $this->hasEntries;
    }

    public function totalResults(): int
    {
        return $this->totalResults;
    }

    /**
     * @return GalleryItem[]
     */
    public function items(): array
    {
        return $this->items;
    }
}
