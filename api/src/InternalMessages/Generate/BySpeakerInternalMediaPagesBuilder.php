<?php

declare(strict_types=1);

namespace App\InternalMessages\Generate;

use App\InternalMessages\InternalMessage;
use App\InternalMessages\InternalMessages;
use App\Pagination\Pagination;
use App\Profiles\Profile;
use Redis;

use function array_flip;
use function json_encode;

readonly class BySpeakerInternalMediaPagesBuilder
{
    public function __construct(
        private Redis $redis,
        private InternalMediaEntryJsonFactory $entryFactory,
    ) {
    }

    public function build(
        Profile $speaker,
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
                speaker: $speaker,
                pagination: $pagination->withCurrentPage(val: $pageNum),
                messages: $messages,
            );
        }

        $this->deleteOrphans(
            existing: $existing->bySpeaker($speaker->slug),
            keep: $pageKeys,
        );
    }

    private function buildPage(
        Profile $speaker,
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

        $key = InternalMediaRedisKey::bySpeakerPage(
            speakerSlug: $speaker->slug,
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
                'byName' => $speaker->fullNameWithHonorific,
                'bySlug' => $speaker->slug,
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
