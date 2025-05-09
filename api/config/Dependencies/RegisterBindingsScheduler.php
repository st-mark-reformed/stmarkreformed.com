<?php

declare(strict_types=1);

namespace Config\Dependencies;

use BuzzingPixel\Scheduler\Framework\RunScheduleSymfonyCommand;
use BuzzingPixel\Scheduler\RedisDriver\RedisScheduleHandler;
use BuzzingPixel\Scheduler\ScheduleHandler;
use Config\ScheduleFactory;
use RxAnte\AppBootstrap\Dependencies\Bindings;

readonly class RegisterBindingsScheduler
{
    public static function register(Bindings $bindings): void
    {
        $bindings->addBinding(
            \BuzzingPixel\Scheduler\ScheduleFactory::class,
            $bindings->autowire(ScheduleFactory::class),
        );

        $bindings->addBinding(
            ScheduleHandler::class,
            $bindings->autowire(RedisScheduleHandler::class),
        );

        $bindings->addBinding(
            RunScheduleSymfonyCommand::class,
            $bindings->autowire(RunScheduleSymfonyCommand::class)
                ->constructorParameter(
                    'name',
                    'schedule:run',
                ),
        );
    }
}
