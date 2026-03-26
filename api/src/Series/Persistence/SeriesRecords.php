<?php

declare(strict_types=1);

namespace App\Series\Persistence;

use function array_map;
use function array_values;

readonly class SeriesRecords
{
    /** @var SeriesRecord[] */
    public array $records;

    /** @param SeriesRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (SeriesRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(SeriesRecord): T $callback
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
