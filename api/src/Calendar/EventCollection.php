<?php

declare(strict_types=1);

namespace App\Calendar;

use Spatie\Cloneable\Cloneable;

use function array_filter;
use function array_map;
use function array_slice;
use function array_values;
use function assert;
use function count;

readonly class EventCollection
{
    use Cloneable;

    /** @var Event[] */
    private array $events;

    /** @param Event[] $events */
    public function __construct(array $events = [])
    {
        $events = array_values(array_map(
            static fn (Event $event) => $event,
            $events,
        ));

        $finalEvents = [];

        foreach ($events as $event) {
            assert($event instanceof Event);

            if ($event->isMultiDay) {
                $startDateMinus1 = (clone $event->startDate)->modify(
                    '-1 Day',
                );

                for ($i = 1; $i <= $event->totalDays; $i++) {
                    $finalEvents[] = $event->withStartDate(
                        $startDateMinus1->modify(
                            '+' . $i . ' Days',
                        ),
                    );
                }

                continue;
            }

            $finalEvents[] = $event;
        }

        $this->events = $finalEvents;
    }

    public function count(): int
    {
        return count($this->events);
    }

    /** @return mixed[] */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->events);
    }

    public function filter(callable $callback): EventCollection
    {
        return $this->with(events: array_values(array_filter(
            $this->events,
            $callback,
        )));
    }

    public function fromLimit(int $limit): EventCollection
    {
        return $this->with(events: array_slice(
            $this->events,
            0,
            $limit,
        ));
    }

    /** @param Event[] $events */
    public function withEvents(array $events = []): EventCollection
    {
        return $this->with(events: $events);
    }

    /** @return mixed[] */
    public function asScalarArray(): array
    {
        return $this->mapToArray(
            static fn (Event $e) => $e->asScalarArray(),
        );
    }
}
