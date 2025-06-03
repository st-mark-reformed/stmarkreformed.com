<?php

declare(strict_types=1);

namespace Config;

use App\Http\Response\Media\Messages\EnqueueGenerateMessagesPagesForRedis;
use App\Http\Response\Media\Resources\EnqueueGenerateResourcePagesForRedis;
use App\Http\Response\Members\HymnsOfTheMonth\EnqueueGenerateHymnsOfTheMonthPageForRedis;
use App\Http\Response\Members\InternalMedia\EnqueueGenerateInternalMediaPagesForRedis;
use App\Http\Response\News\EnqueueGenerateNewsPagesForRedis;
use App\Http\Response\News\EnqueueGeneratePastorsPagesForRedis;
use App\Http\Response\Publications\EnqueueGenerateMenOfTheMarkPagesForRedis;
use App\MailingLists\CheckMailingLists;
use BuzzingPixel\CraftScheduler\ContainerRetrieval\ContainerItem;
use BuzzingPixel\CraftScheduler\ContainerRetrieval\RetrieveContainersEvent;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\RetrieveScheduleEvent;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\SetDefaultContainerEvent;
use Psr\Container\ContainerInterface;

use function getenv;

// phpcs:disable SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed

class Schedule
{
    public function __construct(private ContainerInterface $di)
    {
    }

    public function retrieveContainers(RetrieveContainersEvent $e): void
    {
        $e->containerConfigItems()->addItem(
            new ContainerItem(
                $this->di::class,
                $this->di,
            ),
        );
    }

    public function setDefaultContainer(SetDefaultContainerEvent $e): void
    {
        $e->setDefaultContainer($this->di::class);
    }

    public function retrieve(RetrieveScheduleEvent $e): void
    {
        $schedule = $e->scheduleConfigItems();

        EnqueueGenerateInternalMediaPagesForRedis::addSchedule(
            $schedule
        );

        EnqueueGenerateHymnsOfTheMonthPageForRedis::addSchedule(
            $schedule,
        );

        EnqueueGenerateMenOfTheMarkPagesForRedis::addSchedule(
            $schedule,
        );

        EnqueueGenerateNewsPagesForRedis::addSchedule(
            $schedule,
        );

        EnqueueGeneratePastorsPagesForRedis::addSchedule(
            $schedule,
        );

        EnqueueGenerateResourcePagesForRedis::addSchedule(
            $schedule,
        );

        EnqueueGenerateMessagesPagesForRedis::addSchedule(
            $schedule,
        );

        if ((bool) getenv('ENABLE_MAILING_LIST_SCHEDULE')) {
            CheckMailingLists::addSchedule($schedule);
        }
    }
}
