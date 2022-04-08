<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use DatePeriod;
use DateTimeImmutable;
use Solspace\Calendar\Elements\Event;

use function assert;

class MonthDayFactory
{
    /**
     * @param DatePeriod<DateTimeImmutable> $monthRange
     */
    public function create(
        string $month,
        DatePeriod $monthRange,
        EventCollection $events,
    ): MonthDayCollection {
        $items = [];

        foreach ($monthRange as $day) {
            assert($day instanceof DateTimeImmutable);

            $monthString = $day->format('Y-m');

            $dateString = $day->format('Y-m-d');

            $daysEvents = $events->filter(
                static function (
                    Event $event,
                ) use ($dateString): bool {
                    $startDate = $event->getStartDate();

                    if ($startDate === null) {
                        return false;
                    }

                    return $startDate->format('Y-m-d') === $dateString;
                }
            );

            $items[] = new MonthDay(
                day: $day,
                events: $daysEvents,
                isActiveMonth: $monthString === $month,
            );
        }

        return new MonthDayCollection($items);
    }
}
