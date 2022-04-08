<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use DateTimeImmutable;

class MonthDay
{
    public function __construct(
        public bool $isActiveMonth,
        public DateTimeImmutable $day,
        public EventCollection $events,
    ) {
    }
}
