<?php

declare(strict_types=1);

namespace App\Uploads;

use function array_map;
use function array_unique;
use function array_values;
use function count;
use function in_array;
use function sort;

readonly class ReceivedChunkIndices
{
    /** @var int[] */
    public array $items;

    /** @param int[] $items */
    public function __construct(array $items)
    {
        $unique = array_values(array_unique(array_map(
            static fn (int $index): int => $index,
            $items,
        )));

        sort($unique);

        $this->items = $unique;
    }

    public function has(int $index): bool
    {
        return in_array($index, $this->items, true);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isComplete(int $totalChunks): bool
    {
        for ($index = 0; $index < $totalChunks; $index++) {
            if (! $this->has($index)) {
                return false;
            }
        }

        return true;
    }

    /** @return int[] */
    public function asArray(): array
    {
        return $this->items;
    }
}
