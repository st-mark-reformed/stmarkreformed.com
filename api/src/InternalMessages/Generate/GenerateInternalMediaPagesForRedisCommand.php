<?php

declare(strict_types=1);

namespace App\InternalMessages\Generate;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class GenerateInternalMediaPagesForRedisCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'internal-messages:generate-redis-pages',
            self::class,
        );
    }

    public function __construct(
        private GenerateInternalMediaPagesForRedis $generate,
    ) {
    }

    public function __invoke(): int
    {
        $this->generate->generate();

        return 0;
    }
}
