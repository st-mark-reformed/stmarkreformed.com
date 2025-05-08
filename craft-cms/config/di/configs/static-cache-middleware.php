<?php

declare(strict_types=1);

use BuzzingPixel\SlimBridge\ServerRequestFactory;
use BuzzingPixel\StaticCache\CacheApi\CacheApiContract;
use BuzzingPixel\StaticCache\CacheApi\RedisCache\RedisCacheApi;
use BuzzingPixel\StaticCache\StaticCacheMiddleware;
use Psr\Container\ContainerInterface;

use function DI\autowire;

return [
    CacheApiContract::class => autowire(RedisCacheApi::class),
    StaticCacheMiddleware::class => static function (
        ContainerInterface $container,
    ): StaticCacheMiddleware {
        $enabled = (bool) getenv('STATIC_CACHE_ENABLED');

        if ($enabled) {
            $requestFactory = $container->get(ServerRequestFactory::class);

            assert($requestFactory instanceof ServerRequestFactory);

            $request = $requestFactory->make();

            $params = $request->getQueryParams();

            $liveKey = (string) ($params['x-craft-live-preview'] ?? '');

            $previewKey = (string) ($params['x-craft-preview'] ?? '');

            $live = $liveKey !== '' || $previewKey !== '';

            $enabled = ! $live;
        }

        $cacheApi = $container->get(CacheApiContract::class);

        assert($cacheApi instanceof RedisCacheApi);

        return new StaticCacheMiddleware(
            $enabled,
            $cacheApi,
        );
    },
];
