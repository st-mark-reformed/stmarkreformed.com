<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use function array_map;
use function array_values;
use function ceil;
use function count;

class MonthDayCollection
{
    /** @var MonthDay[] */
    private array $items = [];

    /**
     * @param MonthDay[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = array_values(array_map(
            static fn (MonthDay $md) => $md,
            $items,
        ));
    }

    /**
     * @return MonthDay[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function rows(): int
    {
        return (int) ceil(count($this->items) / 7);
    }
}
