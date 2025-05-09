<?php

declare(strict_types=1);

namespace Config\Dependencies;

use BuzzingPixel\Queue\Framework\QueueConsumeNextSymfonyCommand;
use BuzzingPixel\Queue\QueueHandler;
use BuzzingPixel\Queue\RedisDriver\RedisQueueHandler;
use RxAnte\AppBootstrap\Dependencies\Bindings;

readonly class RegisterBindingsQueue
{
    public static function register(Bindings $bindings): void
    {
        $bindings->addBinding(
            QueueHandler::class,
            $bindings->resolveFromContainer(RedisQueueHandler::class),
        );

        $bindings->addBinding(
            QueueConsumeNextSymfonyCommand::class,
            $bindings->autowire(QueueConsumeNextSymfonyCommand::class)
                ->constructorParameter(
                    'name',
                    'queue:consume-next',
                ),
        );
    }
}
