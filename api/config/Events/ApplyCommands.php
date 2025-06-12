<?php

declare(strict_types=1);

namespace Config\Events;

use App\Authentication\User\CreateUserCommand;
use App\Authentication\User\ListUsersCommand;
use App\Calendar\GenerateCalendarPages;
use App\Calendar\GenerateUpcomingEvents;
use App\Persistence\CreateDatabaseAndUserCommand;
use App\Persistence\Migrate\MigrateCreateCommand;
use App\Persistence\Migrate\MigrateDownCommand;
use App\Persistence\Migrate\MigrateStatusCommand;
use App\Persistence\Migrate\MigrateUpCommand;
use App\Persistence\Seed\SeedCreateCommand;
use BuzzingPixel\Queue\Framework\QueueConsumeNextSymfonyCommand;
use BuzzingPixel\Scheduler\Framework\RunScheduleSymfonyCommand;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class ApplyCommands
{
    public function onDispatch(ApplyCliCommandsEvent $commands): void
    {
        GenerateCalendarPages::register($commands);
        GenerateUpcomingEvents::register($commands);
        CreateDatabaseAndUserCommand::register($commands);
        SeedCreateCommand::register($commands);
        MigrateUpCommand::register($commands);
        MigrateStatusCommand::register($commands);
        MigrateDownCommand::register($commands);
        MigrateCreateCommand::register($commands);
        CreateUserCommand::register($commands);
        ListUsersCommand::register($commands);

        $commands->addSymfonyCommand(
            QueueConsumeNextSymfonyCommand::class,
        );

        $commands->addSymfonyCommand(
            RunScheduleSymfonyCommand::class,
        );
    }
}
