<?php

declare(strict_types=1);

namespace Config\Events;

use BuzzingPixel\Queue\Framework\QueueConsumeNextSymfonyCommand;
use BuzzingPixel\Scheduler\Framework\RunScheduleSymfonyCommand;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class ApplyCommands
{
    public function onDispatch(ApplyCliCommandsEvent $commands): void
    {
        $commands->addSymfonyCommand(
            QueueConsumeNextSymfonyCommand::class,
        );

        $commands->addSymfonyCommand(
            RunScheduleSymfonyCommand::class,
        );
    }
}
