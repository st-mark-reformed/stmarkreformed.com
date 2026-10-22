<?php

declare(strict_types=1);

use Config\BootConfigFactory;
use Config\BootMiddlewareConfigFactory;
use Config\LaggingPackageDeprecationFilter;
use Config\Paths;
use RxAnte\AppBootstrap\Boot;

require dirname(__DIR__) . '/vendor/autoload.php';

$bootContainer = new Boot()
    ->start(config: new BootConfigFactory()->create(false));

new LaggingPackageDeprecationFilter()->register();

$bootContainer
    ->buildContainer(register: Paths::DEPENDENCIES)
    ->registerEventSubscribers(register: Paths::EVENTS)
    ->buildHttpApplication()
    ->applyRoutes()
    ->applyMiddleware(new BootMiddlewareConfigFactory()->create())
    ->runApplication();
