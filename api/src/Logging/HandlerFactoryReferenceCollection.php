<?php

declare(strict_types=1);

namespace App\Logging;

use function array_map;
use function array_values;

readonly class HandlerFactoryReferenceCollection
{
    /** @var HandlerFactoryReference[] */
    public array $items;

    /** @param HandlerFactoryReference[] $items */
    public function __construct(array $items)
    {
        $this->items = array_values(array_map(
            static fn (HandlerFactoryReference $i) => $i,
            $items,
        ));
    }

    /** @return mixed[] */
    public function map(callable $callback): array
    {
        return array_values(array_map(
            $callback,
            $this->items,
        ));
    }
}
