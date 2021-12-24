<?php

declare(strict_types=1);

namespace App\Messages\SetMessageSeriesLatestEntryDate;

use App\Shared\ElementQueryFactories\CategoryQueryFactory;
use craft\elements\Category;

use function array_map;

class SetMessageSeriesLatestEntry
{
    public function __construct(
        private CategoryQueryFactory $categoryQueryFactory,
        private SetLatestMessageForSeries $setLatestEntryForCategory,
    ) {
    }

    public function set(): void
    {
        $categoryQuery = $this->categoryQueryFactory->make();

        $categoryQuery->group('messageSeries');

        /** @var Category[] $categories */
        $categories = $categoryQuery->all();

        array_map(
            [$this->setLatestEntryForCategory, 'set'],
            $categories,
        );
    }
}
