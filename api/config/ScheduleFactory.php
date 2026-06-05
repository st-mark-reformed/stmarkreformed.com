<?php

declare(strict_types=1);

namespace Config;

use App\Calendar\EnqueueCacheRemoteIcsFile;
use App\Calendar\EnqueueGenerateCalendarPages;
use App\Calendar\EnqueueGenerateUpcomingEvents;
use App\HymnsOfTheMonth\Generate\EnqueueGenerateHymnsOfTheMonthForRedis;
use App\InternalMessages\Generate\EnqueueGenerateInternalMediaPagesForRedis;
use App\MailingLists\Schedule\EnqueueCheckMailingLists;
use App\MenOfTheMark\Generate\EnqueueGenerateMenOfTheMarkPagesForRedis;
use App\Messages\Generate\EnqueueGenerateMessagesPagesForRedis;
use App\Messages\Search\EnqueueIndexAllMessages;
use App\News\Generate\EnqueueGenerateNewsPagesForRedis;
use App\PastorsPage\Generate\EnqueueGeneratePastorsPageForRedis;
use App\Resources\Generate\EnqueueGenerateResourcesPagesForRedis;
use BuzzingPixel\Scheduler\Frequency;
use BuzzingPixel\Scheduler\ScheduleItem;
use BuzzingPixel\Scheduler\ScheduleItemCollection;
use RxAnte\AppBootstrap\RuntimeConfig;

readonly class ScheduleFactory implements \BuzzingPixel\Scheduler\ScheduleFactory
{
    public function __construct(private RuntimeConfig $runtimeConfig)
    {
    }

    public function createSchedule(): ScheduleItemCollection
    {
        $scheduleItems = [
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
            new ScheduleItem(
                runEvery: Frequency::FIVE_MINUTES,
                class: EnqueueGeneratePastorsPageForRedis::class,
                method: 'enqueue',
            ),
            new ScheduleItem(
                runEvery: Frequency::FIVE_MINUTES,
                class: EnqueueGenerateHymnsOfTheMonthForRedis::class,
                method: 'enqueue',
            ),
            new ScheduleItem(
                runEvery: Frequency::FIVE_MINUTES,
                class: EnqueueGenerateResourcesPagesForRedis::class,
                method: 'enqueue',
            ),
        ];

        // The mailing list check connects to live IMAP mailboxes and forwards
        // real mail, so it must never run in local development.
        if (! $this->runtimeConfig->getBoolean(RuntimeConfigOptions::DEV_MODE)) {
            $scheduleItems[] = new ScheduleItem(
                runEvery: Frequency::ALWAYS,
                class: EnqueueCheckMailingLists::class,
                method: 'enqueue',
            );
        }

        return new ScheduleItemCollection(scheduleItems: $scheduleItems);
    }
}
