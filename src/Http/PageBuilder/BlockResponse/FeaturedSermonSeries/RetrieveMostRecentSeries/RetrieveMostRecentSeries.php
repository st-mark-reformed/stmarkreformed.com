<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries\RetrieveMostRecentSeries;

use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModelFactory;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use yii\base\InvalidConfigException;

use function assert;

class RetrieveMostRecentSeries
{
    public function __construct(
        private EntryQueryFactory $entryQueryFactory,
        private AudioPlayerContentModelFactory $playerModelFactory,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function retrieve(): MostRecentSeries
    {
        $sermonQuery = $this->entryQueryFactory->make();

        $sermonQuery->section('messages');

        /**
         * @psalm-suppress UndefinedMagicMethod
         * @phpstan-ignore-next-line
         */
        $sermonQuery->messageSeries(':notempty:');

        $sermon = $sermonQuery->one();

        assert($sermon instanceof Entry);

        $playerContentModel = $this->playerModelFactory->makeFromSermonEntry(
            sermon: $sermon,
        );

        $seriesKeyVal = $playerContentModel->firstSeriesGuarantee();

        return new MostRecentSeries(
            seriesTitle: $seriesKeyVal->value(),
            seriesUrl: $seriesKeyVal->href(),
            playerContentModel: $playerContentModel,
        );
    }
}
