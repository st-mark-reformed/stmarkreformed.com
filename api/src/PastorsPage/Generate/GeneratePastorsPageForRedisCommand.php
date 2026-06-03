<?php

declare(strict_types=1);

namespace App\PastorsPage\Generate;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class GeneratePastorsPageForRedisCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'pastors-page:generate-redis-pages',
            self::class,
        );
    }

    public function __construct(private GeneratePastorsPageForRedis $generate)
    {
    }

    public function __invoke(): int
    {
        $this->generate->generate();

        return 0;
    }
}
