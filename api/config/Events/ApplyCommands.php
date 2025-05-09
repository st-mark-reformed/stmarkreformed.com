<?php

declare(strict_types=1);

namespace Config\Events;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class ApplyCommands
{
    public function onDispatch(ApplyCliCommandsEvent $commands): void
    {
    }
}
