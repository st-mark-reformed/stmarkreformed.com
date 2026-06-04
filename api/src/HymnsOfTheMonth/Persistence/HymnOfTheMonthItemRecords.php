<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence;

use function array_map;
use function array_values;

readonly class HymnOfTheMonthItemRecords
{
    /** @var HymnOfTheMonthItemRecord[] */
    public array $records;

    /** @param HymnOfTheMonthItemRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (HymnOfTheMonthItemRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(HymnOfTheMonthItemRecord): T $callback
     *
     * @return T[]
     *
     * @template T
     */
    public function map(callable $callback): array
    {
        return array_values(array_map(
            $callback,
            $this->records,
        ));
    }
}
