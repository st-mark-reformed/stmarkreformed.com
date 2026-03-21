<?php

declare(strict_types=1);

namespace Config\Dependencies;

use Config\RuntimeConfigOptions;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Redis;
use RxAnte\AppBootstrap\Dependencies\Bindings;
use RxAnte\AppBootstrap\RuntimeConfig;
use Symfony\Component\Cache\Adapter\RedisAdapter;

readonly class CacheBindings
{
    public function __invoke(Bindings $bindings): void
    {
        $bindings->addBinding(
            CacheItemPoolInterface::class,
            $bindings->resolveFromContainer(RedisAdapter::class),
        );

        $bindings->addBinding(
            RedisAdapter::class,
            static function (ContainerInterface $container): RedisAdapter {
                return new RedisAdapter(
                    $container->get(Redis::class),
                    'smrc_api',
                );
            },
        );

        $bindings->addBinding(
            Redis::class,
            static function (ContainerInterface $container): Redis {
                $redis = new Redis();

                $redis->connect(
                    $container->get(RuntimeConfig::class)->getString(
                        RuntimeConfigOptions::REDIS_HOST,
                    ),
                );

                return $redis;
            },
        );
    }
}
