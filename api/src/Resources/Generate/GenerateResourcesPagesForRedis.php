<?php

declare(strict_types=1);

namespace App\Resources\Generate;

use App\Resources\ResourceItem;
use App\Resources\ResourcesRepository;
use Psr\Clock\ClockInterface;
use Redis;
use Throwable;

readonly class GenerateResourcesPagesForRedis
{
    public const string JOB_HANDLE = 'generate-resources-redis-pages';

    public const string JOB_NAME = 'Generate Resources Redis Pages';

    private const int PER_PAGE = 12;

    public function __construct(
        private Redis $redis,
        private ClockInterface $clock,
        private ResourcesRepository $repository,
        private ResourcePagesBuilder $resourcePagesBuilder,
    ) {
    }

    public function generate(): void
    {
        $enabled = $this->repository->findAll()->filter(
            callback: static fn (ResourceItem $item): bool => $item->isEnabled,
        );

        $now = $this->clock->now();

        // Live items appear in the listing; future-dated items only get a
        // permalink (slug) key until their date arrives.
        $liveItems = $enabled->filter(
            callback: static fn (ResourceItem $item): bool => $item->date <= $now,
        );

        $futureItems = $enabled->filter(
            callback: static fn (ResourceItem $item): bool => $item->date > $now,
        );

        $existing = new ExistingRedisKeys(
            allKeys: $this->redis->keys(ResourcesRedisKey::allPattern()),
        );

        $this->redis->multi(Redis::PIPELINE);

        try {
            $this->resourcePagesBuilder->build(
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
