<?php

declare(strict_types=1);

namespace App\InternalSeries\Persistence;

use function array_map;
use function array_values;

readonly class InternalSeriesRecords
{
    /** @var InternalSeriesRecord[] */
    public array $records;

    /** @param InternalSeriesRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (InternalSeriesRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(InternalSeriesRecord): T $callback
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
