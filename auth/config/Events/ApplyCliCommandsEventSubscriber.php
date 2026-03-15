<?php

declare(strict_types=1);

namespace Config\Events;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

class ApplyCliCommandsEventSubscriber
{
    public function onDispatch(ApplyCliCommandsEvent $event): void
    {
    }
}
