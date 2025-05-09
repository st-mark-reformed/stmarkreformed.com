<?php

declare(strict_types=1);

namespace App\Calendar;

use Config\SystemTimezone;
use DateInterval;
use DateTimeImmutable;
use Hyperf\Guzzle\ClientFactory;
use ICal\ICal;
use Psr\Clock\ClockInterface;
use Redis;

use function array_map;
use function serialize;

readonly class CacheRemoteIcsFile
{
    public const string ICS_SOURCE = 'https://calendar.google.com/calendar/ical/fb2c50190df2dd072a90e748dacf8d91db65b1d2729b48e5460fc5b219f253dd%40group.calendar.google.com/public/basic.ics';

    private DateTimeImmutable $oneYearAgo;

    private DateTimeImmutable $oneYearAhead;

    public function __construct(
        ClockInterface $clock,
        private Redis $redis,
        private EventFactory $eventFactory,
        private SystemTimezone $systemTimezone,
        private ClientFactory $guzzleClientFactory,
    ) {
        $today = $clock->now()->setTimezone($systemTimezone);

        $oneYear = new DateInterval('P1Y');

        $this->oneYearAgo = $today->sub($oneYear);

        $this->oneYearAhead = $today->add($oneYear);
    }

    public function __invoke(): void
    {
        $this->cache();
    }

    public function cache(): void
    {
        $requestResponse = $this->guzzleClientFactory->create()->get(
            self::ICS_SOURCE,
        );

        $icsContents = $requestResponse->getBody()->getContents();

        $this->redis->set(
            'calendar_data:calendar:ics',
            $icsContents,
        );

        $ical = new ICal(options: [
            'defaultTimezone' => $this->systemTimezone,
        ]);

        $ical->initString($icsContents);

        $icalEvents = $ical->eventsFromRange(
            $this->oneYearAgo->format('Y-m-01 00:00:00'),
            $this->oneYearAhead->format('Y-m-01 00:00:00'),
        );

        $eventCollection = new EventCollection(array_map(
            function (\ICal\Event $event): Event {
                return $this->eventFactory->createFromICalEvent($event);
            },
            $icalEvents,
        ));

        $this->redis->set(
            'calendar_data:calendar:events',
            serialize($eventCollection),
        );
    }
}
