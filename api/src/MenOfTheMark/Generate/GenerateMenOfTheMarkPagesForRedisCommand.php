<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Generate;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class GenerateMenOfTheMarkPagesForRedisCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'men-of-the-mark:generate-redis-pages',
            self::class,
        );
    }

    public function __construct(private GenerateMenOfTheMarkPagesForRedis $generate)
    {
    }

    public function __invoke(): int
    {
        $this->generate->generate();

        return 0;
    }
}
