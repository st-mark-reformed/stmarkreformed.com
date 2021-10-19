<?php

declare(strict_types=1);

return [
    Redis::class => static function (): Redis {
        $redis = new Redis();

        $redis->connect((string) getenv('REDIS_HOST'));

        return $redis;
    },
];
