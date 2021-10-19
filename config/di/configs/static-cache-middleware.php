<?php

declare(strict_types=1);

use BuzzingPixel\StaticCache\CacheApi\CacheApiContract;
use BuzzingPixel\StaticCache\CacheApi\RedisCache\RedisCacheApi;
use BuzzingPixel\StaticCache\StaticCacheMiddleware;

use function DI\autowire;

return [
    CacheApiContract::class => autowire(RedisCacheApi::class),
    StaticCacheMiddleware::class => autowire(
        StaticCacheMiddleware::class
    )->constructorParameter(
        'enabled',
        (bool) getenv('STATIC_CACHE_ENABLED'),
    ),
];
