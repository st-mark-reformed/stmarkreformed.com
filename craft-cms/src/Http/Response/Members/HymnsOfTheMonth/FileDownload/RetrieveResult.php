<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\FileDownload;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use craft\volumes\Local;
use yii\base\InvalidConfigException;

use function array_filter;
use function array_merge;
use function array_values;
use function assert;

class RetrieveResult
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
    public function fromAttributes(
        string $slug,
        string $filePath,
    ): Result {
        if ($slug === '' || $filePath === '') {
            return new Result(hasResult: false);
        }

        $query = $this->entryQueryFactory->make();

        $query->section('hymnsOfTheMonth');

        $query->slug($slug);

        $entry = $query->one();

        if (! ($entry instanceof Entry)) {
            return new Result(hasResult: false);
        }

        $assets = array_merge(
            $this->assetsFieldHandler->getAll(
                element: $entry,
                field: 'hymnOfTheMonthMusic',
            ),
            $this->assetsFieldHandler->getAll(
                element: $entry,
                field: 'hymnOfTheMonthPracticeTracks',
            ),
        );

        $asset = array_values(array_filter(
            $assets,
            static fn (Asset $a) => $a->getPath() === $filePath,
        ))[0] ?? null;

        if ($asset === null) {
            return new Result(hasResult: false);
        }

        $volume = $asset->getVolume();

        assert($volume instanceof Local);

        return new Result(
            hasResult: true,
            mimeType: (string) $asset->getMimeType(),
            pathOnServer: $volume->getRootPath() . '/' . $asset->getPath(),
        );
    }
}
