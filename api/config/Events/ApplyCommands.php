<?php

declare(strict_types=1);

namespace Config\Events;

use App\Calendar\GenerateCalendarPages;
use App\Calendar\GenerateUpcomingEvents;
use App\Persistence\Migrations\MigrateCreateCommand;
use App\Persistence\Migrations\MigrateDownCommand;
use App\Persistence\Migrations\MigrateStatusCommand;
use App\Persistence\Migrations\MigrateUpCommand;
use BuzzingPixel\Queue\Framework\QueueConsumeNextSymfonyCommand;
use BuzzingPixel\Scheduler\Framework\RunScheduleSymfonyCommand;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class ApplyCommands
{
    public function onDispatch(ApplyCliCommandsEvent $commands): void
    {
        GenerateCalendarPages::addCommand(commands: $commands);
        GenerateUpcomingEvents::addCommand(commands: $commands);
        MigrateStatusCommand::register(commands: $commands);
        MigrateUpCommand::register(commands: $commands);
        MigrateDownCommand::register(commands: $commands);
        MigrateCreateCommand::register(commands: $commands);

        $commands->addSymfonyCommand(
            QueueConsumeNextSymfonyCommand::class,
        );

        $commands->addSymfonyCommand(
            RunScheduleSymfonyCommand::class,
        );
    }
}
