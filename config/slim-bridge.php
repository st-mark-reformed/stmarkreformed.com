<?php

declare(strict_types=1);

use Config\di\Container;
use Slim\App;

return [
    'containerInterface' => Container::get(),
    'appCreatedCallback' => static function (App $app): void {
        $routesCallback = require __DIR__ . '/slim/routes.php';
        assert(is_callable($routesCallback));
        $routesCallback($app);

        $middlewaresCallback = require __DIR__ . '/slim/http-middlewares.php';
        assert(is_callable($middlewaresCallback));
        $middlewaresCallback($app);
    },
];
