<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resource;

use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use yii\base\InvalidConfigException;

use function array_map;

class ResourceFactory
{
    public function __construct(
        private GenericHandler $genericHandler,
        private AssetsFieldHandler $assetsFieldHandler,
        private MatrixFieldHandler $matrixFieldHandler,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function makeFromEntry(Entry $entry): ResourceItem
    {
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
            body: $this->genericHandler->getTwigMarkup(
                element: $entry,
                field: 'body',
            ),
            resourceDownloads: $resourceDownloadItems,
        );
    }
}
