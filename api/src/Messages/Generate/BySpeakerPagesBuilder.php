<?php

declare(strict_types=1);

namespace App\Messages\Generate;

use App\Messages\Message;
use App\Messages\Messages;
use App\Pagination\Pagination;
use App\Profiles\Profile;
use Redis;

use function in_array;
use function json_encode;

readonly class BySpeakerPagesBuilder
{
    public function __construct(
        private Redis $redis,
        private MessageEntryJsonFactory $entryFactory,
    ) {
    }

    public function build(Profile $speaker, Messages $messages, int $perPage): void
    {
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

        $this->deleteOrphans(speaker: $speaker, keep: $pageKeys);
    }

    private function buildPage(
        Profile $speaker,
        Pagination $pagination,
        Messages $messages,
    ): string {
        $pageMessages = $messages->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        $entries = $pageMessages->map(
            callback: fn (Message $message): array => $this->entryFactory->create(
                message: $message,
            ),
        );

        $key = MessagesRedisKey::bySpeakerPage(
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

    /** @param string[] $keep */
    private function deleteOrphans(Profile $speaker, array $keep): void
    {
        $existing = $this->redis->keys(
            MessagesRedisKey::bySpeakerPattern(speakerSlug: $speaker->slug),
        );

        foreach ($existing as $key) {
            if (in_array($key, $keep, true)) {
                continue;
            }

            $this->redis->del($key);
        }
    }
}
