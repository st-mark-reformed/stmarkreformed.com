<?php

declare(strict_types=1);

use BuzzingPixel\StaticCache\StaticCacheMiddleware;
use Slim\App;

return static function (App $app): void {
    $app->add(StaticCacheMiddleware::class);
};
