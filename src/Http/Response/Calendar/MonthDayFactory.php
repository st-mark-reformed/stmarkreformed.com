<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use DatePeriod;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Solspace\Calendar\Elements\Event;

use function assert;

class MonthDayFactory
{
    /**
     * @param DatePeriod<DateTimeImmutable> $monthRange
     *
     * @throws Exception
     */
    public function create(
        string $month,
        DatePeriod $monthRange,
        EventCollection $events,
    ): MonthDayCollection {
        $currentTime = new DateTimeImmutable(
            'now',
            new DateTimeZone('US/Central'),
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
                isInPast: $dateInt < $currentDateInt,
                isCurrentDay: $dateInt === $currentDateInt,
                isActiveMonth: $monthString === $month,
            );
        }

        return new MonthDayCollection($items);
    }
}
