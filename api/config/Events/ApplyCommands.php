<?php

declare(strict_types=1);

namespace Config\Events;

use App\Calendar\GenerateCalendarPages;
use App\Calendar\GenerateUpcomingEvents;
use App\InternalMessages\Generate\GenerateInternalMediaPagesForRedisCommand;
use App\Messages\BackfillAudioFileSizesCommand;
use App\Messages\Generate\GenerateMessagesPagesForRedisCommand;
use App\Messages\Search\IndexAllMessagesCommand;
use App\Messages\Search\SetUpIndicesCommand;
use App\News\Generate\GenerateNewsPagesForRedisCommand;
use App\Persistence\Migrations\MigrateCreateCommand;
use App\Persistence\Migrations\MigrateDownCommand;
use App\Persistence\Migrations\MigrateStatusCommand;
use App\Persistence\Migrations\MigrateUpCommand;
use App\Profiles\ResaveAllProfilesCommand;
use App\Transfer\InternalMessages\ImportInternalMessagesFromCraftCommand;
use App\Transfer\InternalSeries\ImportInternalSeriesFromCraftCommand;
use App\Transfer\Messages\ImportMessagesFromCraftCommand;
use App\Transfer\News\ImportNewsFromCraftCommand;
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
        ImportInternalSeriesFromCraftCommand::register(commands: $commands);
        ImportInternalMessagesFromCraftCommand::register(commands: $commands);
        ImportNewsFromCraftCommand::register(commands: $commands);

        // Messages
        SetUpIndicesCommand::register(commands: $commands);
        IndexAllMessagesCommand::register(commands: $commands);
        BackfillAudioFileSizesCommand::register(commands: $commands);
        GenerateMessagesPagesForRedisCommand::register(commands: $commands);

        // Internal Messages
        GenerateInternalMediaPagesForRedisCommand::register(commands: $commands);

        // News
        GenerateNewsPagesForRedisCommand::register(commands: $commands);

        // Profiles
        ResaveAllProfilesCommand::register(commands: $commands);

        $commands->addSymfonyCommand(
            QueueConsumeNextSymfonyCommand::class,
        );

        $commands->addSymfonyCommand(
            RunScheduleSymfonyCommand::class,
        );
    }
}
