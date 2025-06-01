<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Resource\ResourceDownloadItem;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use craft\elements\Asset;
use craft\elements\Entry;

use craft\elements\MatrixBlock;
use function array_map;
use function count;

class RetrieveResources
{
    public function __construct(
        private GenericHandler $genericHandler,
        private EntryQueryFactory $entryQueryFactory,
        private AssetsFieldHandler $assetsFieldHandler,
        private MatrixFieldHandler $matrixFieldHandler,
    ) {
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
            function (Entry $entry): ResourceItem {
                $resourceDownloads = $this->matrixFieldHandler->getAll(
                    element: $entry,
                    field: 'resourceDownloads',
                );

                $resourceAssets = array_map(
                    fn (MatrixBlock $b) => $this->assetsFieldHandler->getOne(
                        element: $b,
                        field: 'file',
                    ),
                    $resourceDownloads,
                );

                $resourceDownloadItems = array_map(
                    static fn (Asset $a) => ResourceDownloadItem::fromAsset(
                        asset: $a,
                    ),
                    $resourceAssets,
                );

                return new ResourceItem(
                    title: (string) $entry->title,
                    url: (string) $entry->getUrl(),
                    slug: (string) $entry->slug,
                    body: $this->genericHandler->getTwigMarkup(
                        element: $entry,
                        field: 'body',
                    ),
                    resourceDownloads: $resourceDownloadItems,
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
