<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\DownloadAudio;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use craft\volumes\Local;
use yii\base\InvalidConfigException;

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
    public function fromSlug(string $slug): Result
    {
        if ($slug === '') {
            return new Result(hasResult: false);
        }

        $query = $this->entryQueryFactory->make();

        $query->section('internalMessages');

        $query->slug($slug);

        $entry = $query->one();

        if (! ($entry instanceof Entry)) {
            return new Result(hasResult: false);
        }

        $asset = $this->assetsFieldHandler->getOneOrNull(
            element: $entry,
            field: 'internalAudio',
        );

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
