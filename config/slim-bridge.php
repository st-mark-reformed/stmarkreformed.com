<?php

declare(strict_types=1);

use Config\di\Container;
use Slim\App;

return [
    'containerInterface' => Container::get(),
    'appCreatedCallback' => static function (App $app): void {
        /** @psalm-suppress MissingFile */
        $routesCallback = require __DIR__ . '/slim/routes.php';
        /** @psalm-suppress RedundantCondition */
        assert(is_callable($routesCallback));
        $routesCallback($app);

        /** @psalm-suppress MissingFile */
        $middlewaresCallback = require __DIR__ . '/slim/http-middlewares.php';
        /** @psalm-suppress RedundantCondition */
        assert(is_callable($middlewaresCallback));
        $middlewaresCallback($app);
    },
];
