<?php

declare(strict_types=1);

namespace App\Calendar\Month;

use App\Calendar\EventCollection;
use RxAnte\DateImmutable;

readonly class MonthDay
{
    public function __construct(
        public bool $isInPast,
        public bool $isCurrentDay,
        public bool $isActiveMonth,
        public DateImmutable $day,
        public EventCollection $events,
    ) {
    }

    /** @return mixed[] */
    public function asScalarArray(): array
    {
        return [
            'isInPast' => $this->isInPast,
            'isCurrentDay' => $this->isCurrentDay,
            'isActiveMonth' => $this->isActiveMonth,
            'ymd' => $this->day->format('Y-m-d'),
            'year' => (int) $this->day->format('Y'),
            'month' => (int) $this->day->format('m'),
            'day' => (int) $this->day->format('d'),
            'events' => $this->events->asScalarArray(),
        ];
    }
}
