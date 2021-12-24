<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;

use function array_map;
use function count;

class RetrieveHymns
{
    public function __construct(
        private GenericHandler $genericHandler,
        private EntryQueryFactory $entryQueryFactory,
    ) {
    }

    /**
     * @throws InvalidFieldException
     */
    public function retrieve(): HymnResults
    {
        $query = $this->entryQueryFactory->make();

        $query->section('hymnsOfTheMonth');

        $totalResults = (int) $query->count();

        $entries = $query->all();

        $text = 'Resources and tools for learning the hymn of the month: ';

        $results = array_map(
            fn (Entry $entry) => new HymnItem(
                href: (string) $entry->getUrl(),
                title: (string) $entry->title,
                content: $text . $this->genericHandler->getString(
                    element: $entry,
                    field: 'hymnPsalmName',
                ),
            ),
            $entries,
        );

        return new HymnResults(
            hasResults: count($results) > 0,
            totalResults: $totalResults,
            incomingItems: $results,
        );
    }
}
