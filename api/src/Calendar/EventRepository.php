<?php

declare(strict_types=1);

namespace App\Calendar;

use DateInterval;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Redis;

use function assert;
use function in_array;
use function is_string;
use function unserialize;

readonly class EventRepository
{
    public function __construct(
        private Redis $redis,
        private ClockInterface $clock,
    ) {
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

    public function getUpcomingEvents(int $limit = 8): EventCollection
    {
        $from = $this->clock->now()->sub(
            new DateInterval('PT8H'),
        );

        $eventHashStore = [];

        return $this->getAllEventsFromCache()
            ->filter(
                static function (Event $event) use (
                    $from,
                    &$eventHashStore,
                ): bool {
                    $startTimestamp = $event->startDate->getTimestamp();

                    $isInTimeframe = $startTimestamp > $from->getTimestamp();

                    if (! $isInTimeframe) {
                        return false;
                    }

                    if (
                        in_array(
                            $event->uid,
                            $eventHashStore,
                            true,
                        )
                    ) {
                        return false;
                    }

                    $eventHashStore[] = $event->uid;

                    return true;
                },
            )
            ->fromLimit($limit);
    }
}
