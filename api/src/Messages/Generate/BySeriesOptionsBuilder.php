<?php

declare(strict_types=1);

namespace App\Messages\Generate;

use App\Series\Series;
use Redis;

use function json_encode;
use function ksort;

readonly class BySeriesOptionsBuilder
{
    public function __construct(private Redis $redis)
    {
    }

    /** @param Series[] $seriesWithMessages */
    public function build(array $seriesWithMessages): void
    {
        $options = [];

        foreach ($seriesWithMessages as $series) {
            $options[$series->slug->toString()] = $series->title;
        }

        ksort($options);

        $this->redis->set(
            MessagesRedisKey::bySeriesOptions(),
            json_encode($options),
        );
    }
}
