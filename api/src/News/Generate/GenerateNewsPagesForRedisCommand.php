<?php

declare(strict_types=1);

namespace App\News\Generate;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class GenerateNewsPagesForRedisCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'news:generate-redis-pages',
            self::class,
        );
    }

    public function __construct(private GenerateNewsPagesForRedis $generate)
    {
    }

    public function __invoke(): int
    {
        $this->generate->generate();

        return 0;
    }
}
