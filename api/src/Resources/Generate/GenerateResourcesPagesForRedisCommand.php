<?php

declare(strict_types=1);

namespace App\Resources\Generate;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class GenerateResourcesPagesForRedisCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'resources:generate-redis-pages',
            self::class,
        );
    }

    public function __construct(private GenerateResourcesPagesForRedis $generate)
    {
    }

    public function __invoke(): int
    {
        $this->generate->generate();

        return 0;
    }
}
