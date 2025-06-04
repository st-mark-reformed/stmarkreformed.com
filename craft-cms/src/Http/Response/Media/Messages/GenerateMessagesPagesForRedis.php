<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages;

use App\Http\Pagination\Pagination;
use App\Messages\MessagesApi;
use App\Messages\RetrieveMessages\MessageRetrievalParams;
use App\Shared\ElementQueryFactories\CategoryQueryFactory;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Category;
use craft\elements\Entry;
use Redis;

class GenerateMessagesPagesForRedis
{
    private const PER_PAGE = 25;

    public function __construct(
        private Redis $redis,
        private MessagesApi $messagesApi,
        private CategoryQueryFactory $queryFactory,
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
            ->section('messages')
            ->count();

        $pagination = (new Pagination())
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: 1)
            ->withTotalResults($totalResults);

        $totalPages = $pagination->totalPages();

        $generatedKeys = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $generatedKeys[] = 'messages:page:' . $page;
            $this->generatePage(
                $pagination->withCurrentPage($page)
            );
        }

        $existingPageKeys = $this->redis->keys('messages:page:*');

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
            'messages:slug:*'
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

        $this->generateMostRecentSeries();
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

        $series = $entry->messageSeries->one();

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

        $audioFile = $entry->audio->one();

        $audioFileName = $audioFile?->filename;

        return [
            'uid' => $entry->uid,
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
        $currentPage = $pagination->currentPage();

        $results = $this->messagesApi->retrieveMessages(
            new MessageRetrievalParams(
                limit: self::PER_PAGE,
                offset: ($currentPage * self::PER_PAGE) - self::PER_PAGE,
            ),
        );

        $entries = $results->map(function (Entry $entry) {
            return $this->createJsonArrayFromEntry($entry);
        });

        array_map(
            function (array $entry) {
                $key = 'messages:slug:' . $entry['slug'];

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
            'messages:page:' . $pagination->currentPage(),
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
        int    $profileId,
        string $profileSlug,
    ): void {
        $totalResults = (int) $this->entryQueryFactory->make()
            ->section('messages')
            ->profile($profileId)
            ->count();

        $pagination = (new Pagination())
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: 1)
            ->withTotalResults($totalResults);

        $totalPages = $pagination->totalPages();

        $generatedKeys = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $generatedKeys[] = 'messages:by:' . $profileSlug . ':' . $page;
            $this->generateByPage(
                $pagination->withCurrentPage($page),
                $profileSlug,
            );
        }

        $existingPageKeys = $this->redis->keys(
            'messages:by:' . $profileSlug . ':*'
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
        string $profileSlug,
    ): void {
        $currentPage = $pagination->currentPage();

        $results = $this->messagesApi->retrieveMessages(
            new MessageRetrievalParams(
                limit: self::PER_PAGE,
                offset: ($currentPage * self::PER_PAGE) - self::PER_PAGE,
                by: [$profileSlug],
            ),
        );

        $first = $results->first();

        $byProfile = $first->profile->one();
        assert($byProfile instanceof Entry);

        $entries = $results->map(function (Entry $entry) {
            return $this->createJsonArrayFromEntry($entry);
        });

        $this->redis->set(
            'messages:by:' . $byProfile->slug . ':' . $currentPage,
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
        string $seriesSlug
    ): void {
        $totalResults = (int) $this->entryQueryFactory->make()
            ->section('messages')
            ->messageSeries($seriesId)
            ->count();

        $pagination = (new Pagination())
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: 1)
            ->withTotalResults($totalResults);

        $totalPages = $pagination->totalPages();

        $generatedKeys = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $generatedKeys[] = 'messages:series:' . $seriesSlug . ':' . $page;
            $this->generateSeriesPage(
                $pagination->withCurrentPage($page),
                $seriesSlug,
            );
        }

        $existingPageKeys = $this->redis->keys(
            'messages:series:' . $seriesSlug . ':*'
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
        string $seriesSlug,
    ): void {
        $currentPage = $pagination->currentPage();

        $results = $this->messagesApi->retrieveMessages(
            new MessageRetrievalParams(
                limit: self::PER_PAGE,
                offset: ($currentPage * self::PER_PAGE) - self::PER_PAGE,
                series: [$seriesSlug],
            ),
        );

        $first = $results->first();

        $series = $first->messageSeries->one();
        assert($series instanceof Category);

        $entries = $results->map(function (Entry $entry) {
            return $this->createJsonArrayFromEntry($entry);
        });

        $this->redis->set(
            'messages:series:' . $series->slug . ':' . $currentPage,
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

    private function generateMostRecentSeries(): void
    {
        $query = $this->queryFactory->make();

        $query->group('messageSeries');

        $query->orderBy('latestEntryAt desc');

        $query->limit(6);

        /** @phpstan-ignore-next-line */
        $query->excludeFromFeatured(false);

        $results = array_map(
            static fn (Category $c) => [
                'title' => $c->title,
                'slug' => $c->slug,
            ],
            $query->all(),
        );

        $this->redis->set(
            'messages:most_recent_series',
            json_encode($results),
        );
    }
}
