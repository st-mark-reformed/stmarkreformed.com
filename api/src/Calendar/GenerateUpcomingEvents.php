<?php

declare(strict_types=1);

namespace App\Calendar;

use Redis;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

use function json_encode;

readonly class GenerateUpcomingEvents
{
    public const string JOB_HANDLE = 'generate-upcoming-events';

    public const string JOB_NAME = 'Generate Upcoming Events';

    public static function addCommand(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'calendar:generate-upcoming',
            self::class,
        );
    }

    public function __construct(
        private Redis $redis,
        private EventRepository $eventRepository,
    ) {
    }

    public function __invoke(): void
    {
        $this->generate();
    }

    public function generate(): void
    {
        $events = $this->eventRepository->getUpcomingEvents();

        $this->redis->set(
            'calendar_data:calendar:upcoming_events',
            json_encode($events->asScalarArray()),
        );
    }
}
