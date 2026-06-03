<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Persistence;

use function array_map;
use function array_values;

readonly class MenOfTheMarkItemRecords
{
    /** @var MenOfTheMarkItemRecord[] */
    public array $records;

    /** @param MenOfTheMarkItemRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (MenOfTheMarkItemRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(MenOfTheMarkItemRecord): T $callback
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
