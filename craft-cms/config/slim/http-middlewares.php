<?php

declare(strict_types=1);

use App\Http\AppMiddleware\HoneyPotMiddleware;
use App\Http\Response\Error\HttpErrorAction;
use BuzzingPixel\Minify\MinifyMiddleware;
use BuzzingPixel\StaticCache\StaticCacheMiddleware;
use DI\Container;
use Slim\App;

return static function (App $app): void {
    $devMode = (bool) getenv('DEV_MODE');

    $container = $app->getContainer();

    assert($container instanceof Container);

    if (! $devMode) {
        $errorMiddleware = $app->addErrorMiddleware(
            false,
            false,
            false
        );

        $httpErrorAction = $container->get(HttpErrorAction::class);

        assert($httpErrorAction instanceof HttpErrorAction);

        $errorMiddleware->setDefaultErrorHandler($httpErrorAction);
    }

    $app->add(MinifyMiddleware::class);
    $app->add(HoneyPotMiddleware::class);
    $app->add(StaticCacheMiddleware::class);
};
