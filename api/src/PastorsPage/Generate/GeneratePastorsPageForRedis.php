<?php

declare(strict_types=1);

namespace App\PastorsPage\Generate;

use App\PastorsPage\PastorsPageItem;
use App\PastorsPage\PastorsPageRepository;
use Psr\Clock\ClockInterface;
use Redis;
use Throwable;

readonly class GeneratePastorsPageForRedis
{
    public const string JOB_HANDLE = 'generate-pastors-page-redis-pages';

    public const string JOB_NAME = 'Generate Pastors Page Redis Pages';

    private const int PER_PAGE = 12;

    public function __construct(
        private Redis $redis,
        private ClockInterface $clock,
        private PastorsPageRepository $repository,
        private PastorsPageBuilder $pastorsPageBuilder,
    ) {
    }

    public function generate(): void
    {
        $enabled = $this->repository->findAll()->filter(
            callback: static fn (
                PastorsPageItem $pastorsPageItem,
            ): bool => $pastorsPageItem->isEnabled,
        );

        $now = $this->clock->now();

        // Only items whose date has arrived are written. Future-dated items get
        // no key at all — unlike News, a Pastor's Page permalink must not exist
        // before the entry's date.
        $liveItems = $enabled->filter(
            callback: static fn (
                PastorsPageItem $pastorsPageItem,
            ): bool => $pastorsPageItem->date <= $now,
        );

        $existing = new ExistingRedisKeys(
            allKeys: $this->redis->keys(PastorsPageRedisKey::allPattern()),
        );

        $this->redis->multi(Redis::PIPELINE);

        try {
            $this->pastorsPageBuilder->build(
                liveItems: $liveItems,
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
