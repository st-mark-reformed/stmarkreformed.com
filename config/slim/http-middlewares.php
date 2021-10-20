<?php

declare(strict_types=1);

use App\Http\AppMiddleware\HoneyPotMiddleware;
use BuzzingPixel\Minify\MinifyMiddleware;
use BuzzingPixel\StaticCache\StaticCacheMiddleware;
use Slim\App;

return static function (App $app): void {
    $app->add(MinifyMiddleware::class);
    $app->add(HoneyPotMiddleware::class);
    $app->add(StaticCacheMiddleware::class);
};
