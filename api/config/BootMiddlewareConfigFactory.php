<?php

declare(strict_types=1);

namespace Config;

use RxAnte\AppBootstrap\Http\BootHttpMiddlewareConfig;

class BootMiddlewareConfigFactory
{
    public function create(): BootHttpMiddlewareConfig
    {
        $runtimeConfig = new RuntimeConfig();

        return new BootHttpMiddlewareConfig(
            useProductionErrorMiddleware: $runtimeConfig->getBoolean(
                RuntimeConfigOptions::USE_PRODUCTION_ERROR_MIDDLEWARE,
            ),
        );
    }
}
