<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence;

use function array_map;
use function array_values;

readonly class InternalMessagesRecords
{
    /** @var InternalMessageRecord[] */
    public array $records;

    /** @param InternalMessageRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (InternalMessageRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(InternalMessageRecord): T $callback
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
