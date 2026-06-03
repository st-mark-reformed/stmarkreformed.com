<?php

declare(strict_types=1);

namespace App\PastorsPage\Persistence;

use function array_map;
use function array_values;

readonly class PastorsPageItemRecords
{
    /** @var PastorsPageItemRecord[] */
    public array $records;

    /** @param PastorsPageItemRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (PastorsPageItemRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(PastorsPageItemRecord): T $callback
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
