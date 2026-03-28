<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use function array_map;
use function array_values;

readonly class MessagesRecords
{
    /** @var MessageRecord[] */
    public array $records;

    /** @param MessageRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (MessageRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(MessageRecord): T $callback
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
