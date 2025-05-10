<?php

declare(strict_types=1);

namespace App\Calendar\Month;

use function array_map;
use function array_values;
use function ceil;
use function count;

readonly class MonthDayCollection
{
    /** @var MonthDay[] */
    private array $items;

    /** @param MonthDay[] $items */
    public function __construct(array $items = [])
    {
        $this->items = array_values(array_map(
            static fn (MonthDay $md) => $md,
            $items,
        ));
    }

    public function rows(): int
    {
        return (int) ceil(count($this->items) / 7);
    }

    /** @return mixed[] */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->items);
    }

    /** @return mixed[] */
    public function asScalarArray(): array
    {
        return $this->mapToArray(
            static fn (MonthDay $d) => $d->asScalarArray(),
        );
    }
}
