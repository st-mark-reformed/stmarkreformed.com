<?php

declare(strict_types=1);

namespace App\Http\Response\News;

use App\Http\Pagination\Pagination;
use App\Http\Response\News\NewsItem\CompileResponse;
use App\Http\Response\News\NewsList\NewsItem;
use App\Http\Response\News\NewsList\NewsResults;
use App\Http\Response\News\NewsList\RetrieveNewsItems;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\EntryBuilder\ExtractBodyContent;
use App\Shared\Utility\TruncateFactory;
use craft\elements\Entry;
use DateTimeInterface;
use Redis;

class GenerateNewsTypePagesForRedis
{
    private const PER_PAGE = 12;

    public function __construct(
        private Redis $redis,
        private CompileResponse $compileResponse,
        private TruncateFactory $truncateFactory,
        private EntryQueryFactory $entryQueryFactory,
        private RetrieveNewsItems $retrieveNewsItems,
        private ExtractBodyContent $extractBodyContent,
    ) {
    }

    private array $pageSlugKeys = [];

    public function generate(
        string $section,
    ): void
    {
        $this->pageSlugKeys = [];

        $totalResults = (int) $this->entryQueryFactory->make()
            ->section($section)
            ->count();

        $pagination = (new Pagination())
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: 1)
            ->withTotalResults($totalResults);

        $totalPages = $pagination->totalPages();

        $generatedKeys = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $generatedKeys[] = $section . ':page:' . $page;
            $this->generatePage(
                pagination: $pagination->withCurrentPage($page),
                section: $section,
            );
        }

        $this->generateFutureEntriesPages($section);

        $existingPageKeys = $this->redis->keys($section . ':page:*');

        foreach ($existingPageKeys as $key) {
            if (!in_array($key, $generatedKeys, true)) {
                $this->redis->del($key);
            }
        }

        $existingSlugKeys = $this->redis->keys(
            $section . ':slug:*'
        );

        foreach ($existingSlugKeys as $key) {
            if (
                !in_array(
                    $key,
                    $this->pageSlugKeys,
                    true
                )
            ) {
                $this->redis->del($key);
            }
        }
    }

    private function createJsonArrayFromNewsItem (NewsItem $newsItem): array
    {
        return [
            'uid' => $newsItem->uid(),
            'title' => $newsItem->title(),
            'slug' => $newsItem->slug(),
            'excerpt' => $newsItem->excerpt(),
            'content' => $newsItem->content(),
            'bodyOnlyContent' => $newsItem->bodyOnlyContent(),
            'readableDate' => $newsItem->readableDate(),
            'postDate' => $newsItem->postDate()->format(
                DateTimeInterface::RFC2822,
            ),
        ];
    }

    private function generatePage(
        Pagination $pagination,
        string $section,
    ): void {
        $results = $this->retrieveNewsItems->retrieve(
            pagination: $pagination,
            section: $section,
        );

        $entries = $results->mapItems(function (NewsItem $newsItem) {
            return $this->createJsonArrayFromNewsItem($newsItem);
        });

        array_map(
            function (array $entry) use ($section) {
                $key = $section . ':slug:' . $entry['slug'];

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
            $section . ':page:' . $pagination->currentPage(),
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

    private function generateFutureEntriesPages(string $section): void
    {
        $query = $this->entryQueryFactory->make();

        $query->status('pending');

        $query->section($section);

        $results = $query->all();

        $items = array_map(
            fn (Entry $entry) => new NewsItem(
                uid: (string) $entry->uid,
                title: (string) $entry->title,
                slug: (string) $entry->slug,
                excerpt: $this->truncateFactory->make(300)->truncate(
                    $this->extractBodyContent->fromElementWithEntryBuilder(
                        element: $entry
                    ),
                ),
                content: $this->compileResponse->fromEntry($entry),
                bodyOnlyContent: $this->extractBodyContent->fromElementWithEntryBuilder(
                    $entry,
                ),
                url: (string) $entry->getUrl(),
                /** @phpstan-ignore-next-line */
                readableDate: $entry->postDate->format('F jS, Y'),
                postDate: $entry->postDate,
            ),
            $results,
        );

        $newsResults = new NewsResults(
            hasEntries: count($results) > 0,
            totalResults: count($results),
            incomingItems: $items,
        );

        $entries = $newsResults->mapItems(function (NewsItem $newsItem) {
            return $this->createJsonArrayFromNewsItem($newsItem);
        });

        array_map(
            function (array $entry) use ($section) {
                $key = $section . ':slug:' . $entry['slug'];

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
    }
}
