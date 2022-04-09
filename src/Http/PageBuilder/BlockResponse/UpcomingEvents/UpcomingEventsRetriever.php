<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\UpcomingEvents;

use App\Shared\ElementQueryFactories\CalendarEventQueryFactory;
use Carbon\Carbon;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Solspace\Calendar\Elements\Event;

use function assert;

class UpcomingEventsRetriever
{
    public function __construct(
        private CalendarEventQueryFactory $calendarEventQueryFactory,
    ) {
    }

    /**
     * @return Event[]
     */
    public function retrieve(): array
    {
        $current = new DateTimeImmutable(
            'now',
            new DateTimeZone('US/Central'),
        );

        $startCarbon = Carbon::createFromInterface(
            $current->sub(new DateInterval('PT8H')),
        );

        assert($startCarbon instanceof Carbon);

        return $this->calendarEventQueryFactory->make()
            ->setCalendar('stMarkEvents')
            ->setRangeStart($startCarbon)
            ->limit(8)
            ->all();
    }
}
