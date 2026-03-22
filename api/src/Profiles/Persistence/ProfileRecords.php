<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use function array_map;
use function array_values;

readonly class ProfileRecords
{
    /** @var ProfileRecord[] */
    public array $records;

    /** @param ProfileRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (ProfileRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(ProfileRecord): T $callback
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
