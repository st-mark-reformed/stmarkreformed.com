<?php

declare(strict_types=1);

namespace Config\Events;

use App\Healthcheck;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

class ApplyRoutesEventSubscriber
{
    public function onDispatch(ApplyRoutesEvent $routes): void
    {
        Healthcheck::applyRoute($routes);
    }
}
