<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia;

use App\Http\Pagination\Pagination;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Entry;
use Redis;

class GenerateInternalMediaPagesForRedis
{
    private const PER_PAGE = 25;

    public function __construct(
        private Redis $redis,
        private RetrieveMedia $retrieveMedia,
        private EntryQueryFactory $entryQueryFactory,
    ) {
    }

    public function __invoke(): void
    {
        $this->generate();
    }

    private array $pageSlugKeys = [];

    public function generate(): void
    {
        $this->pageSlugKeys = [];

        $totalResults = (int) $this->entryQueryFactory->make()
            ->section('internalMessages')
            ->count();

        $pagination = (new Pagination())
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: 1)
            ->withTotalResults($totalResults);

        $totalPages = $pagination->totalPages();

        $generatedKeys = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $generatedKeys[] = 'members:internal_media:page:' . $page;
            $this->generatePage(
                $pagination->withCurrentPage($page)
            );
        }

        $existingPageKeys = $this->redis->keys(
            'members:internal_media:page:*'
        );

        foreach ($existingPageKeys as $key) {
            if (!in_array($key, $generatedKeys, true)) {
                $this->redis->del($key);
            }
        }

        $existingSlugKeys = $this->redis->keys(
            'members:internal_media:slug:*'
        );

        foreach ($existingSlugKeys as $key) {
            if (!in_array($key, $this->pageSlugKeys, true)) {
                $this->redis->del($key);
            }
        }
    }

    private function createJsonArrayFromEntry (Entry $entry): array
    {
        $by = $entry->profile->one();

        if ($by !== null) {
            $by = [
                'title' => $by->fullNameHonorific(),
                'slug' => $by->slug,
            ];
        }

        $series = $entry->internalMessageSeries->one();

        if ($series !== null) {
            $series = [
                'title' => $series->title,
                'slug' => $series->slug,
            ];
        }

        $audioFile = $entry->internalAudio->one();

        $audioFileName = $audioFile?->filename;

        return [
            'title' => $entry->title,
            'slug' => $entry->slug,
            'postDate' => $entry->postDate->format('Y-m-d H:i:s'),
            'postDateDisplay' => $entry->postDate->format('F j, Y'),
            'by' => $by,
            'text' => $entry->messageText,
            'series' => $series,
            'audioFileName' => $audioFileName,
        ];
    }

    private function generatePage(Pagination $pagination): void
    {
        $results = $this->retrieveMedia->retrieve(pagination: $pagination);

        $entries = $results->mapItems(function (Entry $entry) {
            return $this->createJsonArrayFromEntry($entry);
        });

        array_map(
            function (array $entry) {
                $key = 'members:internal_media:slug:' . $entry['slug'];

                $this->pageSlugKeys[] = $key;


                $this->redis->set(
                    $key,
                    json_encode([
                        'entry' => $entry,
                    ]),
                );
            },
            $entries,
        );

        $this->redis->set(
            'members:internal_media:page:' . $pagination->currentPage(),
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
    }
}
