<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use function array_map;
use function array_values;
use function count;

class MonthEventsOnlyCollection
{
    /** @var MonthEvent[] */
    private array $items = [];

    /**
     * @param MonthEvent[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = array_values(array_map(
            static fn (MonthEvent $me) => $me,
            $items,
        ));
    }

    /**
     * @return MonthEvent[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }
}
