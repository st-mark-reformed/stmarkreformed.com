<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence;

use function array_map;
use function array_values;

readonly class MailingListRecords
{
    /** @var MailingListRecord[] */
    public array $records;

    /** @param MailingListRecord[] $records */
    public function __construct(array $records)
    {
        $this->records = array_values(array_map(
            static fn (MailingListRecord $r) => $r,
            $records,
        ));
    }

    /**
     * @param callable(MailingListRecord): T $callback
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
