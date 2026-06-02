<?php

declare(strict_types=1);

namespace App\News\Generate;

use App\News\NewsItem;
use App\News\NewsItems;
use App\Pagination\Pagination;
use Redis;

use function array_flip;
use function json_encode;

readonly class NewsPagesBuilder
{
    public function __construct(
        private Redis $redis,
        private NewsEntryJsonFactory $entryFactory,
    ) {
    }

    /**
     * Writes listing page keys (with their slug entries) for the live items,
     * plus slug-only keys for future-dated items so their permalink is
     * reachable before they appear in the listing. Stale page/slug keys are
     * pruned afterward.
     */
    public function build(
        NewsItems $liveItems,
        NewsItems $futureItems,
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
            $pageKeys[] = NewsRedisKey::page(pageNum: $pageNum);

            foreach (
                $this->buildPage(
                    pagination: $pagination->withCurrentPage(val: $pageNum),
                    liveItems: $liveItems,
                ) as $slugKey
            ) {
                $slugKeys[] = $slugKey;
            }
        }

        foreach ($futureItems->items as $newsItem) {
            $slugKeys[] = $this->writeSlugEntry(newsItem: $newsItem);
        }

        $this->deleteOrphans(existing: $existing->pageKeys, keep: $pageKeys);
        $this->deleteOrphans(existing: $existing->slugKeys, keep: $slugKeys);
    }

    /** @return string[] keys of slug entries written for this page */
    private function buildPage(Pagination $pagination, NewsItems $liveItems): array
    {
        $pageItems = $liveItems->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        $entries = $pageItems->map(
            callback: fn (NewsItem $newsItem): array => $this->entryFactory->create(
                newsItem: $newsItem,
            ),
        );

        $slugKeys = [];

        foreach ($entries as $entry) {
            $slugKey    = NewsRedisKey::slug(newsSlug: $entry['slug']);
            $slugKeys[] = $slugKey;

            $this->redis->set(
                $slugKey,
                json_encode(['entry' => $entry]),
            );
        }

        $this->redis->set(
            NewsRedisKey::page(pageNum: $pagination->currentPage()),
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

    private function writeSlugEntry(NewsItem $newsItem): string
    {
        $entry = $this->entryFactory->create(newsItem: $newsItem);

        $slugKey = NewsRedisKey::slug(newsSlug: $entry['slug']);

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
