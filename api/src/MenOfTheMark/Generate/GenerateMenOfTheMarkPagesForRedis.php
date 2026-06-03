<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Generate;

use App\MenOfTheMark\MenOfTheMarkItem;
use App\MenOfTheMark\MenOfTheMarkRepository;
use Psr\Clock\ClockInterface;
use Redis;
use Throwable;

readonly class GenerateMenOfTheMarkPagesForRedis
{
    public const string JOB_HANDLE = 'generate-men-of-the-mark-redis-pages';

    public const string JOB_NAME = 'Generate Men of the Mark Redis Pages';

    public function __construct(
        private Redis $redis,
        private ClockInterface $clock,
        private MenOfTheMarkRepository $repository,
        private MenOfTheMarkIndexBuilder $indexBuilder,
    ) {
    }

    public function generate(): void
    {
        $enabled = $this->repository->findAll()->filter(
            callback: static fn (MenOfTheMarkItem $item): bool => $item->isEnabled,
        );

        $now = $this->clock->now();

        // Live items appear in the listing; future-dated items only get a
        // permalink (slug) key until their date arrives.
        $liveItems = $enabled->filter(
            callback: static fn (MenOfTheMarkItem $item): bool => $item->date <= $now,
        );

        $futureItems = $enabled->filter(
            callback: static fn (MenOfTheMarkItem $item): bool => $item->date > $now,
        );

        $existing = new ExistingRedisKeys(
            allKeys: $this->redis->keys(MenOfTheMarkRedisKey::allPattern()),
        );

        $this->redis->multi(Redis::PIPELINE);

        try {
            $this->indexBuilder->build(
                liveItems: $liveItems,
                futureItems: $futureItems,
                existing: $existing,
            );
        } catch (Throwable $error) {
            $this->redis->discard();

            throw $error;
        }

        $this->redis->exec();
    }
}
