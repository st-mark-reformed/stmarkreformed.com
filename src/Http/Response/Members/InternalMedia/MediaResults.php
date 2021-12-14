<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia;

use craft\elements\Entry;

use function array_map;

class MediaResults
{
    /** @var Entry[] */
    private array $items = [];

    /**
     * @param Entry[] $incomingEntries
     */
    public function __construct(
        private bool $hasEntries,
        private int $totalResults,
        array $incomingEntries,
    ) {
        array_map(
            function (Entry $entry): void {
                $this->items[] = $entry;
            },
            $incomingEntries,
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
     * @return Entry[]
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
