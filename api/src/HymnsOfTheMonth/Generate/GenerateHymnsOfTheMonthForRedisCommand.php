<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Generate;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class GenerateHymnsOfTheMonthForRedisCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'hymns-of-the-month:generate-redis-pages',
            self::class,
        );
    }

    public function __construct(private GenerateHymnsOfTheMonthForRedis $generate)
    {
    }

    public function __invoke(): int
    {
        $this->generate->generate();

        return 0;
    }
}
