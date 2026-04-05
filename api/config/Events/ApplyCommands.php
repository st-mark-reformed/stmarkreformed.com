<?php

declare(strict_types=1);

namespace Config\Events;

use App\Calendar\GenerateCalendarPages;
use App\Calendar\GenerateUpcomingEvents;
use App\Messages\Search\SetUpIndicesCommand;
use App\Persistence\Migrations\MigrateCreateCommand;
use App\Persistence\Migrations\MigrateDownCommand;
use App\Persistence\Migrations\MigrateStatusCommand;
use App\Persistence\Migrations\MigrateUpCommand;
use App\Transfer\Messages\ImportMessagesFromCraftCommand;
use App\Transfer\Profiles\ImportProfilesFromCraftCommand;
use App\Transfer\Series\ImportSeriesFromCraftCommand;
use BuzzingPixel\Queue\Framework\QueueConsumeNextSymfonyCommand;
use BuzzingPixel\Scheduler\Framework\RunScheduleSymfonyCommand;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class ApplyCommands
{
    public function onDispatch(ApplyCliCommandsEvent $commands): void
    {
        // Calendar
        GenerateCalendarPages::addCommand(commands: $commands);
        GenerateUpcomingEvents::addCommand(commands: $commands);

        // Migrations
        MigrateStatusCommand::register(commands: $commands);
        MigrateUpCommand::register(commands: $commands);
        MigrateDownCommand::register(commands: $commands);
        MigrateCreateCommand::register(commands: $commands);

        // Transfer
        ImportProfilesFromCraftCommand::register(commands: $commands);
        ImportSeriesFromCraftCommand::register(commands: $commands);
        ImportMessagesFromCraftCommand::register(commands: $commands);

        // Messages
        SetUpIndicesCommand::register(commands: $commands);

        $commands->addSymfonyCommand(
            QueueConsumeNextSymfonyCommand::class,
        );

        $commands->addSymfonyCommand(
            RunScheduleSymfonyCommand::class,
        );
    }
}
