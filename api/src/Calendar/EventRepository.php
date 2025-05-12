<?php

declare(strict_types=1);

namespace App\Calendar;

use DateInterval;
use DateTimeImmutable;
use Redis;

use function assert;
use function in_array;
use function is_string;
use function unserialize;

readonly class EventRepository
{
    public function __construct(private Redis $redis)
    {
    }

    private function getAllEventsFromCache(): EventCollection
    {
        $allEventCache = $this->redis->get('calendar_data:calendar:events');

        if (! is_string($allEventCache)) {
            return new EventCollection();
        }

        $allEvents = unserialize($allEventCache);

        if (! ($allEvents instanceof EventCollection)) {
            return new EventCollection();
        }

        return $allEvents;
    }

    public function getEventsForMonthPadded(string $month): EventCollection
    {
        $currentMonthObj = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $month . '-01',
        );
        assert($currentMonthObj instanceof DateTimeImmutable);

        $previousMonthObj = $currentMonthObj->sub(
            new DateInterval('P1M'),
        );

        $nextMonthObj = $currentMonthObj->add(
            new DateInterval('P1M'),
        );

        return $this->getAllEventsFromCache()->filter(
            static function (Event $event) use (
                $month,
                $previousMonthObj,
                $nextMonthObj,
            ): bool {
                return in_array(
                    $event->startDate->format('Y-m'),
                    [
                        $month,
                        $previousMonthObj->format('Y-m'),
                        $nextMonthObj->format('Y-m'),
                    ],
                    true,
                );
            },
        );
    }

    public function getEventsForMonth(string $month): EventCollection
    {
        return $this->getAllEventsFromCache()->filter(
            static function (Event $event) use ($month): bool {
                return $event->startDate->format('Y-m') === $month;
            },
        );
    }
}
