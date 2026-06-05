<?php

declare(strict_types=1);

namespace App\Resources\Generate;

use App\Pagination\Pagination;
use App\Resources\ResourceItem;
use App\Resources\ResourceItems;
use Redis;

use function array_flip;
use function json_encode;

readonly class ResourcePagesBuilder
{
    public function __construct(
        private Redis $redis,
        private ResourceEntryJsonFactory $entryFactory,
    ) {
    }

    /**
     * Writes listing page keys (with their slug entries) for the live items,
     * plus slug-only keys for future-dated items so their permalink is
     * reachable before they appear in the listing. Stale page/slug keys are
     * pruned afterward.
     */
    public function build(
        ResourceItems $liveItems,
        ResourceItems $futureItems,
        int $perPage,
        ExistingRedisKeys $existing,
    ): void {
        $pagination = new Pagination()
            ->withPerPage(val: $perPage)
            ->withCurrentPage(val: 1)
            ->withTotalResults(val: $liveItems->count());

        $totalPages = $pagination->totalPages();

        $pageKeys = [];
        $slugKeys = [];

        for ($pageNum = 1; $pageNum <= $totalPages; $pageNum++) {
            $pageKeys[] = ResourcesRedisKey::page(pageNum: $pageNum);

            foreach (
                $this->buildPage(
                    pagination: $pagination->withCurrentPage(val: $pageNum),
                    liveItems: $liveItems,
                ) as $slugKey
            ) {
                $slugKeys[] = $slugKey;
            }
        }

        foreach ($futureItems->items as $resourceItem) {
            $slugKeys[] = $this->writeSlugEntry(resourceItem: $resourceItem);
        }

        $this->deleteOrphans(existing: $existing->pageKeys, keep: $pageKeys);
        $this->deleteOrphans(existing: $existing->slugKeys, keep: $slugKeys);
    }

    /** @return string[] keys of slug entries written for this page */
    private function buildPage(
        Pagination $pagination,
        ResourceItems $liveItems,
    ): array {
        $pageItems = $liveItems->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        $entries = $pageItems->map(
            callback: fn (ResourceItem $resourceItem): array => $this->entryFactory->create(
                resourceItem: $resourceItem,
            ),
        );

        $slugKeys = [];

        foreach ($entries as $entry) {
            $slugKey    = ResourcesRedisKey::slug(resourceSlug: $entry['slug']);
            $slugKeys[] = $slugKey;

            $this->redis->set(
                $slugKey,
                json_encode(['entry' => $entry]),
            );
        }

        $this->redis->set(
            ResourcesRedisKey::page(pageNum: $pagination->currentPage()),
            json_encode([
                'currentPage' => $pagination->currentPage(),
                'perPage' => $pagination->perPage(),
                'totalResults' => $pagination->totalResults(),
                'totalPages' => $pagination->totalPages(),
                'pagesArray' => $pagination->pagesArray(),
                'prevPageLink' => $pagination->prevPageLink(),
                'nextPageLink' => $pagination->nextPageLink(),
                'firstPageLink' => $pagination->firstPageLink(),
                'lastPageLink' => $pagination->lastPageLink(),
                'entries' => $entries,
            ]),
        );

        return $slugKeys;
    }

    private function writeSlugEntry(ResourceItem $resourceItem): string
    {
        $entry = $this->entryFactory->create(resourceItem: $resourceItem);

        $slugKey = ResourcesRedisKey::slug(resourceSlug: $entry['slug']);

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
