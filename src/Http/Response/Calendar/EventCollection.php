<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use Solspace\Calendar\Elements\Event;

use function array_filter;
use function array_map;
use function count;

class EventCollection
{
    /** @var Event[] */
    private array $events = [];

    /**
     * @param Event[] $events
     */
    public function __construct(array $events = [])
    {
        foreach ($events as $event) {
            if ($event->isMultiDay()) {
                /** @phpstan-ignore-next-line */
                $totalDays =  $event->startDate->diffInDays($event->endDate) + 1;

                $startDateMinus1Day = (clone $event->startDate)->modify('-1 Day');

                for ($i = 1; $i <= $totalDays; $i++) {
                    $newEvent = clone $event;

                    $newEvent->startDate = (clone $startDateMinus1Day)->modify('+' . $i . ' Days');

                    $this->events[] = $newEvent;
                }

                continue;
            }

            $this->events[] = $event;
        }
    }

    public function count(): int
    {
        return count($this->events);
    }

    /**
     * @return Event[]
     */
    public function events(): array
    {
        return $this->events;
    }

    public function filter(callable $callback): self
    {
        $newSelf = new self();

        $newSelf->events = array_filter(
            $this->events,
            $callback,
        );

        return $newSelf;
    }

    /**
     * @return mixed[]
     */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->events);
    }
}
