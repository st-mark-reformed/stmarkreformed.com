<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Generate;

use App\MenOfTheMark\MenOfTheMarkItem;
use App\MenOfTheMark\MenOfTheMarkItems;
use Redis;

use function array_flip;
use function json_encode;

readonly class MenOfTheMarkIndexBuilder
{
    public function __construct(
        private Redis $redis,
        private MenOfTheMarkEntryJsonFactory $entryFactory,
    ) {
    }

    /**
     * Writes the single `:index` listing key from the live items, plus a
     * `:slug:{slug}` key per item so each permalink is reachable. Future-dated
     * items get a slug-only key (reachable by permalink) but are kept out of the
     * listing until their date arrives. Stale slug keys are pruned afterward.
     */
    public function build(
        MenOfTheMarkItems $liveItems,
        MenOfTheMarkItems $futureItems,
        ExistingRedisKeys $existing,
    ): void {
        $entries = $liveItems->map(
            callback: fn (MenOfTheMarkItem $item): array => $this->entryFactory
                ->create(menOfTheMarkItem: $item),
        );

        $this->redis->set(
            MenOfTheMarkRedisKey::index(),
            json_encode(['entries' => $entries]),
        );

        $slugKeys = [];

        foreach ($liveItems->items as $item) {
            $slugKeys[] = $this->writeSlugEntry(menOfTheMarkItem: $item);
        }

        foreach ($futureItems->items as $item) {
            $slugKeys[] = $this->writeSlugEntry(menOfTheMarkItem: $item);
        }

        $this->deleteOrphans(existing: $existing->slugKeys, keep: $slugKeys);
    }

    private function writeSlugEntry(MenOfTheMarkItem $menOfTheMarkItem): string
    {
        $entry = $this->entryFactory->create(menOfTheMarkItem: $menOfTheMarkItem);

        $slugKey = MenOfTheMarkRedisKey::slug(slug: $entry['slug']);

        $this->redis->set(
            $slugKey,
            json_encode(['entry' => $entry]),
        );

        return $slugKey;
    }

    /**
     * @param string[] $existing
     * @param string[] $keep
     */
    private function deleteOrphans(array $existing, array $keep): void
    {
        $keepSet = array_flip($keep);

        foreach ($existing as $key) {
            if (isset($keepSet[$key])) {
                continue;
            }

            $this->redis->del($key);
        }
    }
}
