<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

use App\Shared\ElementQueryFactories\CategoryQueryFactory;
use craft\elements\Category;

use function array_map;
use function in_array;

class RetrieveSeriesOptions
{
    public function __construct(private CategoryQueryFactory $queryFactory)
    {
    }

    /**
     * @param string[] $selectedSlugs
     */
    public function retrieve(array $selectedSlugs = []): OptionGroup
    {
        $query = $this->queryFactory->make();

        $query->group('messageSeries');

        $query->orderBy('title asc');

        $series = $query->all();

        $options = array_map(
            static fn (Category $category) => new SelectOption(
                name: (string) $category->title,
                slug: (string) $category->slug,
                isActive: in_array(
                    (string) $category->slug,
                    $selectedSlugs,
                    true,
                ),
            ),
            $series,
        );

        return new OptionGroup(
            groupTitle: '',
            selectOptions: $options,
        );
    }
}
