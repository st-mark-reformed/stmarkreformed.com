<?php

declare(strict_types=1);

namespace App\News\Generate;

use App\News\NewsItem;
use App\News\NewsRepository;
use Psr\Clock\ClockInterface;
use Redis;
use Throwable;

readonly class GenerateNewsPagesForRedis
{
    public const string JOB_HANDLE = 'generate-news-redis-pages';

    public const string JOB_NAME = 'Generate News Redis Pages';

    private const int PER_PAGE = 12;

    public function __construct(
        private Redis $redis,
        private ClockInterface $clock,
        private NewsRepository $repository,
        private NewsPagesBuilder $newsPagesBuilder,
    ) {
    }

    public function generate(): void
    {
        $enabled = $this->repository->findAll()->filter(
            callback: static fn (NewsItem $newsItem): bool => $newsItem->isEnabled,
        );

        $now = $this->clock->now();

        // Live items appear in the listing; future-dated items only get a
        // permalink (slug) key until their date arrives.
        $liveItems = $enabled->filter(
            callback: static fn (NewsItem $newsItem): bool => $newsItem->date <= $now,
        );

        $futureItems = $enabled->filter(
            callback: static fn (NewsItem $newsItem): bool => $newsItem->date > $now,
        );

        $existing = new ExistingRedisKeys(
            allKeys: $this->redis->keys(NewsRedisKey::allPattern()),
        );

        $this->redis->multi(Redis::PIPELINE);

        try {
            $this->newsPagesBuilder->build(
                liveItems: $liveItems,
                futureItems: $futureItems,
                perPage: self::PER_PAGE,
                existing: $existing,
            );
        } catch (Throwable $error) {
            $this->redis->discard();

            throw $error;
        }

        $this->redis->exec();
    }
}
