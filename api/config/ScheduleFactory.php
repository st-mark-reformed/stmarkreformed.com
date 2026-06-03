<?php

declare(strict_types=1);

namespace Config;

use App\Calendar\EnqueueCacheRemoteIcsFile;
use App\Calendar\EnqueueGenerateCalendarPages;
use App\Calendar\EnqueueGenerateUpcomingEvents;
use App\InternalMessages\Generate\EnqueueGenerateInternalMediaPagesForRedis;
use App\MenOfTheMark\Generate\EnqueueGenerateMenOfTheMarkPagesForRedis;
use App\Messages\Generate\EnqueueGenerateMessagesPagesForRedis;
use App\Messages\Search\EnqueueIndexAllMessages;
use App\News\Generate\EnqueueGenerateNewsPagesForRedis;
use BuzzingPixel\Scheduler\Frequency;
use BuzzingPixel\Scheduler\ScheduleItem;
use BuzzingPixel\Scheduler\ScheduleItemCollection;

readonly class ScheduleFactory implements \BuzzingPixel\Scheduler\ScheduleFactory
{
    public function createSchedule(): ScheduleItemCollection
    {
        return new ScheduleItemCollection(scheduleItems: [
            new ScheduleItem(
                runEvery: Frequency::FIVE_MINUTES,
                class: EnqueueCacheRemoteIcsFile::class,
            ),
            new ScheduleItem(
                runEvery: Frequency::FIVE_MINUTES,
                class: EnqueueGenerateCalendarPages::class,
            ),
            new ScheduleItem(
                runEvery: Frequency::FIVE_MINUTES,
                class: EnqueueGenerateUpcomingEvents::class,
            ),
            new ScheduleItem(
                runEvery: Frequency::FIVE_MINUTES,
                class: EnqueueIndexAllMessages::class,
                method: 'enqueue',
            ),
            new ScheduleItem(
                runEvery: Frequency::FIVE_MINUTES,
                class: EnqueueGenerateMessagesPagesForRedis::class,
                method: 'enqueue',
            ),
            new ScheduleItem(
                runEvery: Frequency::FIVE_MINUTES,
                class: EnqueueGenerateInternalMediaPagesForRedis::class,
                method: 'enqueue',
            ),
            new ScheduleItem(
                runEvery: Frequency::FIVE_MINUTES,
                class: EnqueueGenerateNewsPagesForRedis::class,
                method: 'enqueue',
            ),
            new ScheduleItem(
                runEvery: Frequency::FIVE_MINUTES,
                class: EnqueueGenerateMenOfTheMarkPagesForRedis::class,
                method: 'enqueue',
            ),
        ]);
    }
}
