<?php

declare(strict_types=1);

use Config\BootConfigFactory;
use Config\BootMiddlewareConfigFactory;
use Config\ConfigPath;
use RxAnte\AppBootstrap\Boot;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require dirname(__DIR__) . '/vendor/autoload.php';

new Boot()
    ->start(config: new BootConfigFactory()->create(false))
    ->buildContainer(register: ConfigPath::DEPENDENCIES)
    ->registerEventSubscribers(register: ConfigPath::EVENTS)
    ->buildHttpApplication()
    ->applyRoutes()
    ->applyMiddleware(config: new BootMiddlewareConfigFactory()->create())
    ->runApplication();
