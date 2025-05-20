<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use craft\elements\Asset;
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

        $results = array_map(
            function (Entry $entry) {
                $text = 'Resources and tools for learning the hymn of the month: ';

                $hymnPsalmName = $this->genericHandler->getString(
                    element: $entry,
                    field: 'hymnPsalmName',
                );

                $musicSheet = $entry->hymnOfTheMonthMusic->one();

                $practiceTracks = $entry->hymnOfTheMonthPracticeTracks->all();

                return new HymnItem(
                    href: (string) $entry->getUrl(),
                    title: (string) $entry->title,
                    slug: (string) $entry->slug,
                    hymnPsalmName: $hymnPsalmName,
                    content: $text . $hymnPsalmName,
                    musicSheetFilePath: $musicSheet?->path,
                    practiceTracks: array_map(
                        function (Asset $practiceTrack): HymnItemPracticeTrack {
                            return new HymnItemPracticeTrack(
                                title: $practiceTrack->title,
                                path: $practiceTrack->path,
                            );
                        },
                        $practiceTracks,
                    ),
                );
            },
            $entries,
        );

        return new HymnResults(
            hasResults: count($results) > 0,
            totalResults: $totalResults,
            incomingItems: $results,
        );
    }
}
