<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Solspace\Calendar\Elements\Event;

class MonthEventFactory
{
    /**
     * @throws Exception
     */
    public function create(
        string $monthString,
        EventCollection $events,
    ): MonthEventsOnlyCollection {
        $currentTime = new DateTimeImmutable(
            'now',
            new DateTimeZone('US/Central'),
        );

        $currentDateInt = (int) $currentTime->format('Ymd');

        $monthEventsOnly = $events->filter(
            static function (Event $event) use ($monthString): bool {
                $startDate = $event->getStartDate();

                if ($startDate === null) {
                    return false;
                }

                return $startDate->format('Y-m') === $monthString;
            }
        );

        return new MonthEventsOnlyCollection(
            $monthEventsOnly->mapToArray(
                static function (Event $event) use (
                    $currentDateInt,
                ): MonthEvent {
                    $isInPast = true;

                    $startDate = $event->getStartDate();

                    if ($startDate !== null) {
                        $dateInt = (int) $startDate->format('Ymd');

                        $isInPast = $dateInt < $currentDateInt;
                    }

                    return new MonthEvent(
                        event: $event,
                        isInPast: $isInPast,
                    );
                },
            ),
        );
    }
}
