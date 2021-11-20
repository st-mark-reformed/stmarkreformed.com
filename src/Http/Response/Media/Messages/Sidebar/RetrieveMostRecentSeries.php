<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Sidebar;

use App\Http\Components\Link\Link;
use App\Shared\ElementQueryFactories\CategoryQueryFactory;
use craft\elements\Category;

use function array_map;
use function http_build_query;

class RetrieveMostRecentSeries
{
    public function __construct(private CategoryQueryFactory $queryFactory)
    {
    }

    /**
     * @return Link[]
     */
    public function retrieve(): array
    {
        $query = $this->queryFactory->make();

        $query->group('messageSeries');

        $query->orderBy('latestEntryAt desc');

        $query->limit(6);

        /** @phpstan-ignore-next-line */
        $query->excludeFromFeatured(false);

        return array_map(
            static fn (Category $category) => new Link(
                isEmpty: false,
                content: (string) $category->title,
                href: '/media/messages?' . http_build_query([
                    'series' => [(string) $category->slug],
                ]),
            ),
            $query->all(),
        );
    }
}
