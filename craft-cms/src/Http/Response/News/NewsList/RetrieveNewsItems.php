<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsList;

use App\Http\Pagination\Pagination;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\EntryBuilder\ExtractBodyContent;
use App\Shared\Utility\TruncateFactory;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use yii\base\InvalidConfigException;

use function array_map;
use function count;

class RetrieveNewsItems
{
    public function __construct(
        private TruncateFactory $truncateFactory,
        private EntryQueryFactory $entryQueryFactory,
        private ExtractBodyContent $extractBodyContent,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function retrieve(
        Pagination $pagination,
        string $section = 'news',
    ): NewsResults {
        $perPage = $pagination->perPage();

        $currentPage = $pagination->currentPage();

        $query = $this->entryQueryFactory->make();

        $query->section($section);

        $totalResults = (int) $query->count();

        $query->limit($perPage);

        $query->offset(($currentPage * $perPage) - $perPage);

        $results = $query->all();

        $items = array_map(
            fn (Entry $entry) => new NewsItem(
                title: (string) $entry->title,
                excerpt: $this->truncateFactory->make(300)->truncate(
                    $this->extractBodyContent->fromElementWithEntryBuilder(
                        element: $entry
                    ),
                ),
                url: (string) $entry->getUrl(),
                /** @phpstan-ignore-next-line */
                readableDate: $entry->postDate->format('F jS, Y'),
            ),
            $results,
        );

        return new NewsResults(
            hasEntries: count($items) > 0,
            totalResults: $totalResults,
            incomingItems: $items,
        );
    }
}
