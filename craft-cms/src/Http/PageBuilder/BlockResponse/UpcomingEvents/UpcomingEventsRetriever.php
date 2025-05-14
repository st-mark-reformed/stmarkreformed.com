<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\UpcomingEvents;

use App\Shared\ElementQueryFactories\CalendarEventQueryFactory;
use Carbon\Carbon;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Redis;
use Solspace\Calendar\Elements\Event;

use Throwable;
use function assert;

class UpcomingEventsRetriever
{
    public function __construct(
        private Redis $redis,
        // private CalendarEventQueryFactory $calendarEventQueryFactory,
    ) {
    }

    /**
     * @return UpcomingEvent[]
     *
     * @throws Exception
     */
    public function retrieve(): array
    {
        try {
            $upcomingEventsCache = $this->redis->get(
                'calendar_data:calendar:upcoming_events',
            );

            $upcomingEventsCacheDecoded = json_decode(
                $upcomingEventsCache,
                true
            );

            return array_map(
                function (array $e) {
                    $startDate = DateTimeImmutable::createFromFormat(
                        'Y-m-d H:i:s',
                        $e['startDate'],
                        new DateTimeZone('US/Central'),
                    );

                    $endDate = DateTimeImmutable::createFromFormat(
                        'Y-m-d H:i:s',
                        $e['endDate'],
                        new DateTimeZone('US/Central'),
                    );

                    return new UpcomingEvent(
                        uid: $e['uid'],
                        summary: $e['summary'],
                        description: $e['description'],
                        location: $e['location'],
                        isInPast: $e['isInPast'],
                        startDate: $startDate,
                        endDate: $endDate,
                        isMultiDay: $e['isMultiDay'],
                        isAllDay: $e['isAllDay'],
                        totalDays: $e['totalDays'],
                    );
                },
                $upcomingEventsCacheDecoded,
            );
        } catch (Throwable) {
            return [];
        }

        // $current = new DateTimeImmutable(
        //     'now',
        //     new DateTimeZone('US/Central'),
        // );
        //
        // $startCarbon = Carbon::createFromInterface(
        //     $current->sub(new DateInterval('PT8H')),
        // );
        //
        // assert($startCarbon instanceof Carbon);
        //
        // return $this->calendarEventQueryFactory->make()
        //     ->setCalendar('stMarkEvents')
        //     ->setRangeStart($startCarbon)
        //     ->limit(8)
        //     ->all();
    }
}
