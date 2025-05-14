<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\UpcomingEvents\Entities;

use App\Http\PageBuilder\BlockResponse\UpcomingEvents\UpcomingEvent;
use Solspace\Calendar\Elements\Event;

use function array_map;
use function array_values;

class UpcomingEventsContentModel
{
    /** @var UpcomingEvent[] */
    private array $events;

    /**
     * @param UpcomingEvent[] $events
     */
    public function __construct(
        private string $heading,
        private string $subHeading,
        array $events,
    ) {
        $this->events = array_values(array_map(
            static fn (UpcomingEvent $e) => $e,
            $events,
        ));
    }

    public function heading(): string
    {
        return $this->heading;
    }

    public function subHeading(): string
    {
        return $this->subHeading;
    }

    public function hasHeadings(): bool
    {
        return $this->heading !== '' || $this->subHeading !== '';
    }

    /**
     * @return UpcomingEvent[]
     */
    public function events(): array
    {
        return $this->events;
    }
}
