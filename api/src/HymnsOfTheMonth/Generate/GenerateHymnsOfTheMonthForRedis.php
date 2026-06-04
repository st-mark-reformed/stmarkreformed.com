<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Generate;

use App\HymnsOfTheMonth\HymnOfTheMonthItem;
use App\HymnsOfTheMonth\HymnOfTheMonthItems;
use App\HymnsOfTheMonth\HymnsOfTheMonthRepository;
use Psr\Clock\ClockInterface;
use Redis;
use Throwable;

use function in_array;
use function json_encode;

readonly class GenerateHymnsOfTheMonthForRedis
{
    public const string JOB_HANDLE = 'generate-hymns-of-the-month-redis-pages';

    public const string JOB_NAME = 'Generate Hymns of the Month Redis Pages';

    public function __construct(
        private Redis $redis,
        private ClockInterface $clock,
        private HymnsOfTheMonthRepository $repository,
        private HymnEntryJsonFactory $entryFactory,
    ) {
    }

    public function generate(): void
    {
        $now = $this->clock->now();

        // Only enabled hymns whose month has arrived are published. A future
        // month must not have a permalink before its date, mirroring the
        // Pastor's Page rule.
        $liveItems = $this->repository->findAll()
            ->filter(
                callback: static fn (
                    HymnOfTheMonthItem $item,
                ): bool => $item->isEnabled,
            )
            ->filter(
                callback: static fn (
                    HymnOfTheMonthItem $item,
                ): bool => $item->date <= $now,
            );

        $existingSlugKeys = $this->redis->keys(
            HymnsOfTheMonthRedisKey::slugPattern(),
        );

        $this->redis->multi(Redis::PIPELINE);

        try {
            $keptSlugKeys = $this->writeKeys(liveItems: $liveItems);

            foreach ($existingSlugKeys as $key) {
                if (in_array($key, $keptSlugKeys, true)) {
                    continue;
                }

                $this->redis->del($key);
            }
        } catch (Throwable $error) {
            $this->redis->discard();

            throw $error;
        }

        $this->redis->exec();
    }

    /**
     * Writes the index key and one slug key per live hymn, returning the slug
     * keys that were written so stale ones can be pruned.
     *
     * @return string[]
     */
    private function writeKeys(HymnOfTheMonthItems $liveItems): array
    {
        $keptSlugKeys = [];

        $entries = $liveItems->map(
            function (HymnOfTheMonthItem $item) use (&$keptSlugKeys): array {
                $entry = $this->entryFactory->create(hymnOfTheMonthItem: $item);

                $slugKey        = HymnsOfTheMonthRedisKey::slug(hymnSlug: $item->slug);
                $keptSlugKeys[] = $slugKey;

                $this->redis->set(
                    $slugKey,
                    json_encode(['entry' => $entry]),
                );

                return $entry;
            },
        );

        $this->redis->set(
            HymnsOfTheMonthRedisKey::index(),
            json_encode(['entries' => $entries]),
        );

        return $keptSlugKeys;
    }
}
