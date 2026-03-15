<?php

declare(strict_types=1);

namespace Config\Events;

use App\Persistence\Migrations\MigrateCreateCommand;
use App\Persistence\Migrations\MigrateDownCommand;
use App\Persistence\Migrations\MigrateStatusCommand;
use App\Persistence\Migrations\MigrateUpCommand;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

class ApplyCliCommandsEventSubscriber
{
    public function onDispatch(ApplyCliCommandsEvent $commands): void
    {
        MigrateCreateCommand::register(commands: $commands);
        MigrateDownCommand::register(commands: $commands);
        MigrateStatusCommand::register(commands: $commands);
        MigrateUpCommand::register(commands: $commands);
    }
}
