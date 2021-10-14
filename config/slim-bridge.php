<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\App;

/** @psalm-suppress MissingFile */
$containerBuilder = (new ContainerBuilder())
    ->useAnnotations(true)
    ->useAutowiring(true)
    ->ignorePhpDocErrors(true)
    ->addDefinitions(require __DIR__ . '/di/dependencies.php');

if ((bool) getenv('ENABLE_DI_COMPILATION')) {
    $diCacheDir = dirname(__DIR__) . '/storage/di-cache';

    if (! is_dir($diCacheDir)) {
        mkdir($diCacheDir, 0777, true);
    }

    $containerBuilder->enableCompilation($diCacheDir);

    $containerBuilder->writeProxiesToFile(
        true,
        $diCacheDir
    );
}

/** @noinspection PhpUnhandledExceptionInspection */
$container = $containerBuilder->build();

return [
    'containerInterface' => $container,
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
