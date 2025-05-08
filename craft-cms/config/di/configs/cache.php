<?php

declare(strict_types=1);

use BuzzingPixel\BpCache\Drivers\RedisCacheItemPool;
use Psr\Cache\CacheItemPoolInterface;

use function DI\autowire;

return [
    CacheItemPoolInterface::class => autowire(
        RedisCacheItemPool::class
    ),
    Redis::class => static function (): Redis {
        $redis = new Redis();

        $redis->connect((string) getenv('REDIS_HOST'));

        return $redis;
    },
];
