<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources;

use App\Http\Pagination\Pagination;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Entry;

use function array_map;
use function count;

class RetrieveResources
{
    public function __construct(private EntryQueryFactory $entryQueryFactory)
    {
    }

    public function retrieve(Pagination $pagination): ResourceResults
    {
        $perPage = $pagination->perPage();

        $currentPage = $pagination->currentPage();

        $query = $this->entryQueryFactory->make();

        $query->section('resources');

        $totalResults = (int) $query->count();

        $query->limit($perPage);

        $query->offset(($currentPage * $perPage) - $perPage);

        $results = $query->all();

        $items = array_map(
            static function (Entry $e): ResourceItem {
                return new ResourceItem(
                    title: (string) $e->title,
                    url: (string) $e->getUrl(),
                );
            },
            $results,
        );

        return new ResourceResults(
            hasEntries: count($items) > 0,
            totalResults: $totalResults,
            incomingItems: $items,
        );
    }
}
