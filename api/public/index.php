<?php

declare(strict_types=1);

use Config\BootConfigFactory;
use Config\BootMiddlewareConfigFactory;
use Config\Dependencies\RegisterBindings;
use Config\Events\EventSubscribers;
use RxAnte\AppBootstrap\Boot;

require dirname(__DIR__) . '/vendor/autoload.php';

new Boot()
    ->start(new BootConfigFactory()->create(false))
    ->buildContainer([RegisterBindings::class, 'register'])
    ->registerEventSubscribers([EventSubscribers::class, 'register'])
    ->buildHttpApplication()
    ->applyRoutes()
    ->applyMiddleware(new BootMiddlewareConfigFactory()->create())
    ->runApplication();
