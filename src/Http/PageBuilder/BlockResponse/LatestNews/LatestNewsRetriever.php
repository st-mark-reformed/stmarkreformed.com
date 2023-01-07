<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\LatestNews;

use App\Http\PageBuilder\BlockResponse\LatestNews\Entities\NewsItem;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\EntryBuilder\ExtractBodyContent;
use App\Shared\Utility\TruncateFactory;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use yii\base\InvalidConfigException;

use function array_map;
use function strip_tags;

class LatestNewsRetriever
{
    public function __construct(
        private TruncateFactory $truncateFactory,
        private EntryQueryFactory $entryQueryFactory,
        private ExtractBodyContent $extractBodyContent,
    ) {
    }

    /**
     * @return NewsItem[]
     *
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function retrieve(): array
    {
        return array_map(
            fn (Entry $e) => new NewsItem(
                title: (string) $e->title,
                excerpt: $this->truncateFactory->make(300)->truncate(
                    strip_tags(
                        $this->extractBodyContent->fromElementWithEntryBuilder(
                            element: $e
                        )
                    ),
                ),
                url: (string) $e->getUrl(),
            ),
            $this->entryQueryFactory->make()
                ->section('news')
                ->orderBy('postDate desc')
                ->limit(3)
                ->all(),
        );
    }
}
