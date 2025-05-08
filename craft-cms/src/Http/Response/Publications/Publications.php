<?php

declare(strict_types=1);

namespace App\Http\Response\Publications;

use function array_map;

class Publications
{
    /** @var Publication[] */
    public array $items = [];

    /** @param Publication[] $items */
    public function __construct(array $items = [])
    {
        array_map(
            function (Publication $item): void {
                $this->items[] = $item;
            },
            $items,
        );
    }

    public function first(): Publication
    {
        return $this->items[0];
    }

    public function walk(callable $callback): void
    {
        array_map(
            $callback,
            $this->items,
        );
    }
}
