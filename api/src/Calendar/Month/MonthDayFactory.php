<?php

declare(strict_types=1);

namespace App\Calendar\Month;

use App\Calendar\Event;
use App\Calendar\EventCollection;
use Config\SystemTimezone;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use RxAnte\DateImmutable;

use function assert;

readonly class MonthDayFactory
{
    public function __construct(
        private ClockInterface $clock,
        private SystemTimezone $systemTimezone,
        private MonthRangeFactory $monthRangeFactory,
    ) {
    }

    /** @param string $month 2024-02, 2025-10, etc. */
    public function create(
        string $month,
        EventCollection $events,
    ): MonthDayCollection {
        $monthRange = $this->monthRangeFactory->make($month);

        $currentTime = $this->clock->now()->setTimezone(
            $this->systemTimezone,
        );

        $currentDateInt = (int) $currentTime->format('Ymd');

        $items = [];

        foreach ($monthRange as $day) {
            assert($day instanceof DateTimeImmutable);

            $monthString = $day->format('Y-m');

            $dateString = $day->format('Y-m-d');

            $dateInt = (int) $day->format('Ymd');

            $daysEvents = $events->filter(
                static function (
                    Event $event,
                ) use ($dateString): bool {
                    $startDate = $event->startDate;

                    return $startDate->format('Y-m-d') === $dateString;
                },
            );

            $items[] = new MonthDay(
                day: DateImmutable::createFromFormat(
                    'Y-m-d',
                    $day->format('Y-m-d'),
                ),
                events: $daysEvents,
                isInPast: $dateInt < $currentDateInt,
                isCurrentDay: $dateInt === $currentDateInt,
                isActiveMonth: $monthString === $month,
            );
        }

        return new MonthDayCollection($items);
    }
}
