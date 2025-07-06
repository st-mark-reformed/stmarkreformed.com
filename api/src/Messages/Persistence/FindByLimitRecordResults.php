<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

readonly class FindByLimitRecordResults
{
    public function __construct(
        public int $limit,
        public int $offset,
        public int $absoluteTotalResults,
        public MessageRecords $records,
    ) {
    }
}
