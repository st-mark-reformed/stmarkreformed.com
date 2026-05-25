<?php

declare(strict_types=1);

namespace App\Messages\Generate;

use App\Series\Series;
use Redis;

use function array_map;
use function array_slice;
use function json_encode;

readonly class MostRecentSeriesBuilder
{
    private const string KEY = 'api-messages:most_recent_series';

    public function __construct(private Redis $redis)
    {
    }

    /** @param Series[] $seriesNewestFirst */
    public function build(array $seriesNewestFirst, int $limit): void
    {
        $top = array_slice($seriesNewestFirst, 0, $limit);

        $payload = array_map(
            static fn (Series $series): array => [
                'title' => $series->title,
                'slug' => $series->slug->toString(),
            ],
            $top,
        );

        $this->redis->set(self::KEY, json_encode($payload));
    }
}
