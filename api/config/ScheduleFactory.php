<?php

declare(strict_types=1);

namespace Config;

use App\Calendar\EnqueueCacheRemoteIcsFile;
use App\Calendar\EnqueueGenerateCalendarPages;
use App\Calendar\EnqueueGenerateUpcomingEvents;
use BuzzingPixel\Scheduler\Frequency;
use BuzzingPixel\Scheduler\ScheduleItem;
use BuzzingPixel\Scheduler\ScheduleItemCollection;

readonly class ScheduleFactory implements \BuzzingPixel\Scheduler\ScheduleFactory
{
    public function createSchedule(): ScheduleItemCollection
    {
        return new ScheduleItemCollection([
            new ScheduleItem(
                Frequency::FIVE_MINUTES,
                EnqueueCacheRemoteIcsFile::class,
            ),
            new ScheduleItem(
                Frequency::FIVE_MINUTES,
                EnqueueGenerateCalendarPages::class,
            ),
            new ScheduleItem(
                Frequency::FIVE_MINUTES,
                EnqueueGenerateUpcomingEvents::class,
            ),
        ]);
    }
}
