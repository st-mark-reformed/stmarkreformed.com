<?php

declare(strict_types=1);

namespace App\Queue;

use Redis;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

use function in_array;
use function is_array;

/**
 * Exits 0 once Redis has finished loading its data set, 1 otherwise. While
 * Valkey loads its AOF on startup it accepts connections but answers commands
 * with LOADING errors, so queueConsumer.sh runs this until it passes before
 * consuming. If Redis is not reachable yet, resolving Redis throws, which also
 * exits non-zero.
 */
readonly class RedisReadyCheckCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'queue:redis-ready-check',
            self::class,
        );
    }

    public function __construct(private Redis $redis)
    {
    }

    public function __invoke(): int
    {
        $info = $this->redis->info('persistence');

        if (! is_array($info)) {
            return 1;
        }

        return in_array($info['loading'] ?? null, [0, '0'], true) ? 0 : 1;
    }
}
