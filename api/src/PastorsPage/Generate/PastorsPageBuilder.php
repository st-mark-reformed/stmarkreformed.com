<?php

declare(strict_types=1);

namespace App\PastorsPage\Generate;

use App\Pagination\Pagination;
use App\PastorsPage\PastorsPageItem;
use App\PastorsPage\PastorsPageItems;
use Redis;

use function array_flip;
use function json_encode;

readonly class PastorsPageBuilder
{
    public function __construct(
        private Redis $redis,
        private PastorsPageEntryJsonFactory $entryFactory,
    ) {
    }

    /**
     * Writes listing page keys (with their slug entries) for the live items.
     * Unlike News, future-dated items are not written at all — on the Pastor's
     * Page a permalink must not exist until the entry's date arrives. Stale
     * page/slug keys are pruned afterward.
     */
    public function build(
        PastorsPageItems $liveItems,
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
            $pageKeys[] = PastorsPageRedisKey::page(pageNum: $pageNum);

            foreach (
                $this->buildPage(
                    pagination: $pagination->withCurrentPage(val: $pageNum),
                    liveItems: $liveItems,
                ) as $slugKey
            ) {
                $slugKeys[] = $slugKey;
            }
        }

        $this->deleteOrphans(existing: $existing->pageKeys, keep: $pageKeys);
        $this->deleteOrphans(existing: $existing->slugKeys, keep: $slugKeys);
    }

    /** @return string[] keys of slug entries written for this page */
    private function buildPage(
        Pagination $pagination,
        PastorsPageItems $liveItems,
    ): array {
        $pageItems = $liveItems->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        $entries = $pageItems->map(
            callback: fn (PastorsPageItem $pastorsPageItem): array => $this->entryFactory->create(
                pastorsPageItem: $pastorsPageItem,
            ),
        );

        $slugKeys = [];

        foreach ($entries as $entry) {
            $slugKey    = PastorsPageRedisKey::slug(pastorsPageSlug: $entry['slug']);
            $slugKeys[] = $slugKey;

            $this->redis->set(
                $slugKey,
                json_encode(['entry' => $entry]),
            );
        }

        $this->redis->set(
            PastorsPageRedisKey::page(pageNum: $pagination->currentPage()),
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
