<?php

declare(strict_types=1);

namespace App\News\Persistence;

use function array_map;
use function array_values;

readonly class NewsItemRecords
{
    /** @var NewsItemRecord[] */
    public array $records;

    /** @param NewsItemRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (NewsItemRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(NewsItemRecord): T $callback
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
