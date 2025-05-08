<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\LatestGalleries;

use App\Http\PageBuilder\BlockResponse\LatestGalleries\Entities\GalleryItem;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use yii\base\InvalidConfigException;

use function array_map;
use function assert;

class LatestGalleriesRetriever
{
    public function __construct(private EntryQueryFactory $entryQueryFactory)
    {
    }

    /**
     * @return GalleryItem[]
     *
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function retrieve(): array
    {
        return array_map(
            static function (Entry $e): GalleryItem {
                $galleryQuery = $e->getFieldValue(
                    'gallery',
                );

                assert(
                    $galleryQuery instanceof AssetQuery
                );

                $firstItem = $galleryQuery->one();

                assert($firstItem instanceof Asset);

                return new GalleryItem(
                    keyImageUrl: (string) $firstItem->getUrl(),
                    title: (string) $e->title,
                    url: (string) $e->getUrl(),
                );
            },
            $this->entryQueryFactory->make()
                ->section('galleries')
                ->orderBy('postDate desc')
                ->limit(3)
                ->all(),
        );
    }
}
