<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources;

use function array_map;
use function count;

class ResourceResults
{
    /** @var ResourceItem[] */
    private array $items = [];

    /**
     * @param ResourceItem[] $incomingItems
     */
    public function __construct(
        private bool $hasEntries,
        private int $totalResults,
        array $incomingItems,
    ) {
        array_map(
            function (ResourceItem $item): void {
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
     * @return ResourceItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items());
    }

    /**
     * @return mixed[]
     */
    public function mapItems(callable $callable): array
    {
        return array_map($callable, $this->items());
    }
}
