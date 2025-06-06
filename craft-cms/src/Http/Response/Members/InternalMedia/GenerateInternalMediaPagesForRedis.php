<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia;

use App\Http\Pagination\Pagination;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Category;
use craft\elements\Entry;
use Redis;
use Throwable;

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

    private array $byIds = [];

    private array $seriesIds = [];

    public function generate(): void
    {
        $this->pageSlugKeys = [];
        $this->byIds = [];
        $this->seriesIds = [];

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
            if (
                !in_array(
                    $key,
                    $generatedKeys,
                    true,
                )
            ) {
                $this->redis->del($key);
            }
        }

        $existingSlugKeys = $this->redis->keys(
            'members:internal_media:slug:*'
        );

        foreach ($existingSlugKeys as $key) {
            if (
                !in_array(
                    $key,
                    $this->pageSlugKeys,
                    true,
                )
            ) {
                $this->redis->del($key);
            }
        }

        foreach ($this->byIds as $id => $slug) {
            $this->generateByPages($id,  $slug);
        }

        foreach ($this->seriesIds as $id => $slug) {
            $this->generateSeriesPages($id, $slug);
        }
    }

    private function createJsonArrayFromEntry (Entry $entry): array
    {
        $by = $entry->profile->one();

        if ($by !== null) {
            $id = $by->id;

            if (! isset($this->byIds[$id])) {
                $this->byIds[$id] = $by->slug;
            }

            $by = [
                'title' => $by->fullNameHonorific(),
                'slug' => $by->slug,
            ];
        }

        $series = $entry->internalMessageSeries->one();

        if ($series !== null) {
            $id = $series->id;

            if (! isset($this->seriesIds[$id])) {
                $this->seriesIds[$id] = $series->slug;
            }

            $series = [
                'title' => $series->title,
                'slug' => $series->slug,
            ];
        }

        $audioFile = $entry->internalAudio->one();

        return [
            'uid' => $entry->uid,
            'title' => $entry->title,
            'slug' => $entry->slug,
            'postDate' => $entry->postDate->format('Y-m-d H:i:s'),
            'postDateDisplay' => $entry->postDate->format('F j, Y'),
            'by' => $by,
            'text' => $entry->messageText,
            'series' => $series,
            'audioFileName' => $audioFile?->filename,
            'audioFileSize' => $audioFile?->size,
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

    private function generateByPages(
        int $profileId,
        string $slug,
    ): void {
        $totalResults = (int) $this->entryQueryFactory->make()
            ->section('internalMessages')
            ->profile($profileId)
            ->count();

        $pagination = (new Pagination())
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: 1)
            ->withTotalResults($totalResults);

        $totalPages = $pagination->totalPages();

        $generatedKeys = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $generatedKeys[] = 'members:internal_media:by:' . $slug . ':' . $page;
            $this->generateByPage(
                $pagination->withCurrentPage($page),
                $profileId,
            );
        }

        $existingPageKeys = $this->redis->keys(
            'members:internal_media:by:' . $slug . ':*'
        );

        foreach ($existingPageKeys as $key) {
            if (
                !in_array(
                    $key,
                    $generatedKeys,
                    true,
                )
            ) {
                $this->redis->del($key);
            }
        }
    }

    private function generateByPage(
        Pagination $pagination,
        int $profileId,
    ): void {
        $results = $this->retrieveMedia->retrieve(
            pagination: $pagination,
            profileId: $profileId,
        );

        $first = $results->first();

        $byProfile = $first->profile->one();
        assert($byProfile instanceof Entry);

        $entries = $results->mapItems(function (Entry $entry) {
            return $this->createJsonArrayFromEntry($entry);
        });

        $this->redis->set(
            'members:internal_media:by:' . $byProfile->slug . ':' . $pagination->currentPage(),
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
                'byName' => $byProfile->fullNameHonorific(),
                'bySlug' => $byProfile->slug,
            ]),
        );
    }

    private function generateSeriesPages(
        int $seriesId,
        string $slug
    ) {
        $totalResults = (int) $this->entryQueryFactory->make()
            ->section('internalMessages')
            ->internalMessageSeries($seriesId)
            ->count();

        $pagination = (new Pagination())
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: 1)
            ->withTotalResults($totalResults);

        $totalPages = $pagination->totalPages();

        $generatedKeys = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $generatedKeys[] = 'members:internal_media:series:' . $slug . ':' . $page;
            $this->generateSeriesPage(
                $pagination->withCurrentPage($page),
                $seriesId,
            );
        }

        $existingPageKeys = $this->redis->keys(
            'members:internal_media:series:' . $slug . ':*'
        );

        foreach ($existingPageKeys as $key) {
            if (
                !in_array(
                    $key,
                    $generatedKeys,
                    true
                )
            ) {
                $this->redis->del($key);
            }
        }
    }

    private function generateSeriesPage(
        Pagination $pagination,
        int $seriesId,
    ): void {
        $results = $this->retrieveMedia->retrieve(
            pagination: $pagination,
            seriesId: $seriesId,
        );

        $first = $results->first();

        $series = $first->internalMessageSeries->one();
        assert($series instanceof Category);

        $entries = $results->mapItems(function (Entry $entry) {
            return $this->createJsonArrayFromEntry($entry);
        });

        $this->redis->set(
            'members:internal_media:series:' . $series->slug . ':' . $pagination->currentPage(),
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
                'seriesSlug' => $series->slug,
            ]),
        );
    }
}
