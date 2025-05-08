<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries;

use App\Http\Pagination\Pagination;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use yii\base\InvalidConfigException;

use function array_map;
use function count;

class RetrieveGalleries
{
    public function __construct(
        private EntryQueryFactory $entryQueryFactory,
        private AssetsFieldHandler $assetsFieldHandler,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function retrieve(Pagination $pagination): GalleryResults
    {
        $perPage = $pagination->perPage();

        $currentPage = $pagination->currentPage();

        $query = $this->entryQueryFactory->make();

        $query->section('galleries');

        $totalResults = (int) $query->count();

        $query->limit($perPage);

        $query->offset(($currentPage * $perPage) - $perPage);

        $results = $query->all();

        $items = array_map(
            function (Entry $e): GalleryItem {
                $firstItem = $this->assetsFieldHandler->getOne(
                    element: $e,
                    field: 'gallery',
                );

                return new GalleryItem(
                    keyImageUrl: (string) $firstItem->getUrl(),
                    title: (string) $e->title,
                    url: (string) $e->getUrl(),
                );
            },
            $results,
        );

        return new GalleryResults(
            hasEntries: count($items) > 0,
            totalResults: $totalResults,
            incomingItems: $items,
        );
    }
}
