<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia;

use App\Http\Pagination\Pagination;
use App\Shared\ElementQueryFactories\EntryQueryFactory;

use function count;

class RetrieveMedia
{
    public function __construct(private EntryQueryFactory $entryQueryFactory)
    {
    }

    public function retrieve(Pagination $pagination): MediaResults
    {
        $perPage = $pagination->perPage();

        $currentPage = $pagination->currentPage();

        $query = $this->entryQueryFactory->make();

        $query->section('internalMessages');

        $totalResults = (int) $query->count();

        $query->limit($perPage);

        $query->offset(($currentPage * $perPage) - $perPage);

        $results = $query->all();

        return new MediaResults(
            hasEntries: count($results) > 0,
            totalResults: $totalResults,
            incomingEntries: $results,
        );
    }
}
