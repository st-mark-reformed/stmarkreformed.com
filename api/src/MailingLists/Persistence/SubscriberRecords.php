<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence;

use function array_map;
use function array_values;

readonly class SubscriberRecords
{
    /** @var SubscriberRecord[] */
    public array $records;

    /** @param SubscriberRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (SubscriberRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(SubscriberRecord): T $callback
     *
     * @return T[]
     *
     * @template T
     */
    public function map(callable $callback): array
    {
        return array_values(array_map($callback, $this->records));
    }
}
