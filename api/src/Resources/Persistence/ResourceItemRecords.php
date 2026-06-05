<?php

declare(strict_types=1);

namespace App\Resources\Persistence;

use function array_map;
use function array_values;

readonly class ResourceItemRecords
{
    /** @var ResourceItemRecord[] */
    public array $records;

    /** @param ResourceItemRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (ResourceItemRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(ResourceItemRecord): T $callback
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
