<?php

declare(strict_types=1);

namespace App\Messages\Generate;

use App\Messages\Message;
use App\Messages\Messages;
use App\Pagination\Pagination;
use Redis;

use function array_flip;
use function json_encode;

readonly class MessagesPagesBuilder
{
    public function __construct(
        private Redis $redis,
        private MessageEntryJsonFactory $entryFactory,
    ) {
    }

    public function build(
        Messages $messages,
        int $perPage,
        ExistingRedisKeys $existing,
    ): void {
        $pagination = new Pagination()
            ->withPerPage(val: $perPage)
            ->withCurrentPage(val: 1)
            ->withTotalResults(val: $messages->count());

        $totalPages = $pagination->totalPages();

        $pageKeys = [];
        $slugKeys = [];

        for ($pageNum = 1; $pageNum <= $totalPages; $pageNum++) {
            $pageKeys[] = MessagesRedisKey::page(pageNum: $pageNum);

            foreach (
                $this->buildPage(
                    pagination: $pagination->withCurrentPage(val: $pageNum),
                    messages: $messages,
                ) as $slugKey
            ) {
                $slugKeys[] = $slugKey;
            }
        }

        $this->deleteOrphans(existing: $existing->pageKeys, keep: $pageKeys);
        $this->deleteOrphans(existing: $existing->slugKeys, keep: $slugKeys);
    }

    /** @return string[] keys of slug entries written for this page */
    private function buildPage(Pagination $pagination, Messages $messages): array
    {
        $pageMessages = $messages->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        $entries = $pageMessages->map(
            callback: fn (Message $message): array => $this->entryFactory->create(
                message: $message,
            ),
        );

        $slugKeys = [];

        foreach ($entries as $entry) {
            $slugKey    = MessagesRedisKey::slug(messageSlug: $entry['slug']);
            $slugKeys[] = $slugKey;

            $this->redis->set(
                $slugKey,
                json_encode(['entry' => $entry]),
            );
        }

        $this->redis->set(
            MessagesRedisKey::page(pageNum: $pagination->currentPage()),
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
