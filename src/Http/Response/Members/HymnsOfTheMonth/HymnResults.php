<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth;

use function array_map;

class HymnResults
{
    /** @var HymnItem[] */
    private array $items = [];

    /**
     * @param HymnItem[] $incomingItems
     */
    public function __construct(
        private bool $hasResults,
        private int $totalResults = 0,
        array $incomingItems = [],
    ) {
        array_map(
            function (HymnItem $item): void {
                $this->items[] = $item;
            },
            $incomingItems,
        );
    }

    public function hasResults(): bool
    {
        return $this->hasResults;
    }

    public function totalResults(): int
    {
        return $this->totalResults;
    }

    /**
     * @return HymnItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @return mixed[]
     */
    public function mapItems(callable $callable): array
    {
        return array_map($callable, $this->items());
    }
}
