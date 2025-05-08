<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\UpcomingEvents\Entities;

use Solspace\Calendar\Elements\Event;

use function array_map;
use function array_values;

class UpcomingEventsContentModel
{
    /** @var Event[] */
    private array $events;

    /**
     * @param Event[] $events
     */
    public function __construct(
        private string $heading,
        private string $subHeading,
        array $events,
    ) {
        $this->events = array_values(array_map(
            static fn (Event $e) => $e,
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
     * @return Event[]
     */
    public function events(): array
    {
        return $this->events;
    }
}
