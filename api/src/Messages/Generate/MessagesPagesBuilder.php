<?php

declare(strict_types=1);

namespace App\Messages\Generate;

use App\Messages\Message;
use App\Messages\Messages;
use App\Pagination\Pagination;
use Redis;

use function in_array;
use function json_encode;

readonly class MessagesPagesBuilder
{
    private const string PAGE_KEY_PREFIX = 'api-messages:page:';
    private const string SLUG_KEY_PREFIX = 'api-messages:slug:';

    public function __construct(
        private Redis $redis,
        private MessageEntryJsonFactory $entryFactory,
    ) {
    }

    public function build(Messages $messages, int $perPage): void
    {
        $pagination = new Pagination()
            ->withPerPage(val: $perPage)
            ->withCurrentPage(val: 1)
            ->withTotalResults(val: $messages->count());

        $totalPages = $pagination->totalPages();

        $pageKeys = [];
        $slugKeys = [];

        for ($pageNum = 1; $pageNum <= $totalPages; $pageNum++) {
            $pageKey    = self::PAGE_KEY_PREFIX . $pageNum;
            $pageKeys[] = $pageKey;

            $slugKeys = [
                ...$slugKeys,
                ...$this->buildPage(
                    pagination: $pagination->withCurrentPage(val: $pageNum),
                    messages: $messages,
                ),
            ];
        }

        $this->deleteOrphans(
            pattern: self::PAGE_KEY_PREFIX . '*',
            keep: $pageKeys,
        );

        $this->deleteOrphans(
            pattern: self::SLUG_KEY_PREFIX . '*',
            keep: $slugKeys,
        );
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
            $slugKey    = self::SLUG_KEY_PREFIX . $entry['slug'];
            $slugKeys[] = $slugKey;

            $this->redis->set(
                $slugKey,
                json_encode(['entry' => $entry]),
            );
        }

        $this->redis->set(
            self::PAGE_KEY_PREFIX . $pagination->currentPage(),
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

    /** @param string[] $keep */
    private function deleteOrphans(string $pattern, array $keep): void
    {
        $existing = $this->redis->keys($pattern);

        foreach ($existing as $key) {
            if (in_array($key, $keep, true)) {
                continue;
            }

            $this->redis->del($key);
        }
    }
}
