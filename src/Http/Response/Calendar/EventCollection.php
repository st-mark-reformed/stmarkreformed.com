<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use Solspace\Calendar\Elements\Event;

use function array_filter;
use function array_map;
use function array_values;
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
        $this->events = array_values(array_map(
            static fn (Event $event) => $event,
            $events
        ));
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
        return new self(array_filter(
            $this->events,
            $callback,
        ));
    }
}
