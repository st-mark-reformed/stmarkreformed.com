<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\Single;

use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;

use function array_map;
use function implode;

class GetResult
{
    public function __construct(
        private GenericHandler $genericHandler,
        private AssetsFieldHandler $assetsFieldHandler,
    ) {
    }

    /**
     * @throws InvalidFieldException
     */
    public function fromEntry(Entry $entry): Result
    {
        $date = $this->genericHandler->getDate(
            element: $entry,
            field: 'date',
        );

        $musicSheetAsset = $this->assetsFieldHandler->getOneOrNull(
            element: $entry,
            field: 'hymnOfTheMonthMusic',
        );

        $musicSheetDownloadUrl = null;

        if ($musicSheetAsset !== null) {
            $musicSheetDownloadUrl = implode('/', [
                $entry->getUrl(),
                'file',
                $musicSheetAsset->getPath(),
            ]);
        }

        $trackAssets = $this->assetsFieldHandler->getAll(
            element: $entry,
            field: 'hymnOfTheMonthPracticeTracks',
        );

        $tracks = array_map(
            static fn (Asset $asset) => new Track(
                title: (string) $asset->title,
                url: implode('/', [
                    $entry->getUrl(),
                    'file',
                    $asset->getPath(),
                ]),
            ),
            $trackAssets,
        );

        return new Result(
            month: $date->format('F'),
            year: $date->format('Y'),
            title: $this->genericHandler->getString(
                element: $entry,
                field: 'hymnPsalmName',
            ),
            musicSheetDownloadUrl: $musicSheetDownloadUrl,
            tracks: $tracks,
        );
    }
}
