<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\UpcomingEvents;

use DateTimeImmutable;

class UpcomingEvent
{
    public function __construct(
        public string $uid,
        public string $summary,
        public string $description,
        public string $location,
        public bool $isInPast,
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $endDate,
        public bool $isMultiDay,
        public bool $isAllDay,
        public int $totalDays,
    ) {
    }
}
