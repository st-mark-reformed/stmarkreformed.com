<?php

declare(strict_types=1);

namespace App\Messages\Generate;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class GenerateMessagesPagesForRedisCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'messages:generate-redis-pages',
            self::class,
        );
    }

    public function __construct(private GenerateMessagesPagesForRedis $generate)
    {
    }

    public function __invoke(): int
    {
        $this->generate->generate();

        return 0;
    }
}
