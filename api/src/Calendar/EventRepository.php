<?php

declare(strict_types=1);

namespace App\Calendar;

use Redis;

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

    public function getEventsForMonth(string $month): EventCollection
    {
        return $this->getAllEventsFromCache()->filter(
            static function (Event $event) use ($month): bool {
                return $event->startDate->format('Y-m') === $month;
            },
        );
    }
}
