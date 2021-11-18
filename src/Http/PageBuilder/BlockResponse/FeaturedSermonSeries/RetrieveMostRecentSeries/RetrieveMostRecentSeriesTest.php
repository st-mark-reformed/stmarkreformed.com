<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries\RetrieveMostRecentSeries;

use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModel;
use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModelFactory;
use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerKeyValItem;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\TestCase;
use yii\base\InvalidConfigException;

class RetrieveMostRecentSeriesTest extends TestCase
{
    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testRetrieve(): void
    {
        $sermon = $this->createMock(Entry::class);

        $sermonQuery = $this->createMock(EntryQuery::class);

        $sermonQuery->expects(self::once())
            ->method('section')
            ->with(self::equalTo('messages'))
            ->willReturn($sermonQuery);

        $sermonQuery->expects(self::once())
            ->method('__call')
            ->willReturnCallback(static function (
                string $method,
                array $arguments,
            ): void {
                self::assertSame(
                    'messageSeries',
                    $method,
                );

                self::assertCount(1, $arguments);

                self::assertSame(
                    ':notempty:',
                    $arguments[0],
                );
            });

        $sermonQuery->expects(self::once())
            ->method('one')
            ->willReturn($sermon);

        $entryQueryFactory = $this->createMock(
            EntryQueryFactory::class,
        );

        $entryQueryFactory->method('make')->willReturn(
            $sermonQuery,
        );

        $playerContentModel = new AudioPlayerContentModel(
            href: 'testHref',
            title: 'testTitle',
            subTitle: 'testSubTitle',
            audioFileHref: 'testAudioHref',
            audioFileMimeType: 'testMimeType',
            keyValueItems: [
                new AudioPlayerKeyValItem(
                    key: 'series',
                    value: 'testValue',
                    href: 'testHref',
                ),
            ],
        );

        $playerModelFactory = $this->createMock(
            AudioPlayerContentModelFactory::class,
        );

        $playerModelFactory->expects(self::once())
            ->method('makeFromSermonEntry')
            ->with(self::equalTo($sermon))
            ->willReturn($playerContentModel);

        $retrieve = new RetrieveMostRecentSeries(
            entryQueryFactory: $entryQueryFactory,
            playerModelFactory: $playerModelFactory,
        );

        $series = $retrieve->retrieve();

        self::assertSame(
            'testValue',
            $series->seriesTitle(),
        );

        self::assertSame(
            'testHref',
            $series->seriesUrl(),
        );

        self::assertSame(
            $playerContentModel,
            $series->playerContentModel(),
        );
    }
}
