<?php

declare(strict_types=1);

namespace Config\Events;

use App\Calendar\GenerateCalendarPages;
use App\Calendar\GenerateUpcomingEvents;
use BuzzingPixel\Queue\Framework\QueueConsumeNextSymfonyCommand;
use BuzzingPixel\Scheduler\Framework\RunScheduleSymfonyCommand;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class ApplyCommands
{
    public function onDispatch(ApplyCliCommandsEvent $commands): void
    {
        GenerateCalendarPages::addCommand($commands);
        GenerateUpcomingEvents::addCommand($commands);

        $commands->addSymfonyCommand(
            QueueConsumeNextSymfonyCommand::class,
        );

        $commands->addSymfonyCommand(
            RunScheduleSymfonyCommand::class,
        );
    }
}
