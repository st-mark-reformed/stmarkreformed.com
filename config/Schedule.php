<?php

declare(strict_types=1);

namespace Config;

use App\MailingLists\CheckMailingLists;
use BuzzingPixel\CraftScheduler\ContainerRetrieval\ContainerItem;
use BuzzingPixel\CraftScheduler\ContainerRetrieval\RetrieveContainersEvent;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\RetrieveScheduleEvent;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\SetDefaultContainerEvent;
use Psr\Container\ContainerInterface;

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

        CheckMailingLists::addSchedule($schedule);
    }
}
