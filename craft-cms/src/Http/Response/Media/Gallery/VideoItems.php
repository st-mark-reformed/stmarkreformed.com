<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Gallery;

use function array_map;
use function count;

class VideoItems
{
    /** @var VideoItem[] */
    private array $items;

    /**
     * @param VideoItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = array_map(
            static fn (VideoItem $i) => $i,
            $items,
        );
    }

    /**
     * @return VideoItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @return mixed[]
     */
    public function map(callable $callable): array
    {
        return array_map($callable, $this->items());
    }

    public function count(): int
    {
        return count($this->items);
    }
}
