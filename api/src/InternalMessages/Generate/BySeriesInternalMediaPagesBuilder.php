<?php

declare(strict_types=1);

namespace App\InternalMessages\Generate;

use App\InternalMessages\InternalMessage;
use App\InternalMessages\InternalMessages;
use App\InternalSeries\InternalSeries;
use App\Pagination\Pagination;
use Redis;

use function array_flip;
use function json_encode;

readonly class BySeriesInternalMediaPagesBuilder
{
    public function __construct(
        private Redis $redis,
        private InternalMediaEntryJsonFactory $entryFactory,
    ) {
    }

    public function build(
        InternalSeries $series,
        InternalMessages $messages,
        int $perPage,
        ExistingInternalMediaRedisKeys $existing,
    ): void {
        $pagination = new Pagination()
            ->withPerPage(val: $perPage)
            ->withCurrentPage(val: 1)
            ->withTotalResults(val: $messages->count());

        $totalPages = $pagination->totalPages();

        $pageKeys = [];

        for ($pageNum = 1; $pageNum <= $totalPages; $pageNum++) {
            $pageKeys[] = $this->buildPage(
                series: $series,
                pagination: $pagination->withCurrentPage(val: $pageNum),
                messages: $messages,
            );
        }

        $this->deleteOrphans(
            existing: $existing->bySeries($series->slug->toString()),
            keep: $pageKeys,
        );
    }

    private function buildPage(
        InternalSeries $series,
        Pagination $pagination,
        InternalMessages $messages,
    ): string {
        $pageMessages = $messages->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        $entries = $pageMessages->map(
            callback: fn (InternalMessage $message): array => $this->entryFactory->create(
                message: $message,
            ),
        );

        $seriesSlug = $series->slug->toString();

        $key = InternalMediaRedisKey::bySeriesPage(
            seriesSlug: $seriesSlug,
            pageNum: $pagination->currentPage(),
        );

        $this->redis->set(
            $key,
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
                'seriesName' => $series->title,
                'seriesSlug' => $seriesSlug,
            ]),
        );

        return $key;
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
