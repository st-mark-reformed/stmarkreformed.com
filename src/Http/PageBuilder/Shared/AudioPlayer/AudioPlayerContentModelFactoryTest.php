<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\Shared\AudioPlayer;

use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\db\AssetQuery;
use craft\elements\db\CategoryQuery;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use DateTime;
use DateTimeInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use yii\base\InvalidConfigException;

class AudioPlayerContentModelFactoryTest extends TestCase
{
    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testMakeFromSermonEntry(): void
    {
        $speaker1 = $this->createMock(Entry::class);

        $speaker1->slug = 'test-slug-1';

        $speaker1->title = 'Test Full Name 1';

        $speaker1->method('getFieldValue')->willReturnCallback(
            static function (string $fieldHandle): string {
                return match ($fieldHandle) {
                    'titleOrHonorific' => 'Test Title 1',
                    default => throw new Exception(),
                };
            }
        );

        $speaker2 = $this->createMock(Entry::class);

        $speaker2->slug = 'test-slug-2';

        $speaker2->title = 'Test Full Name 2';

        $speaker2->method('getFieldValue')->willReturnCallback(
            static function (string $fieldHandle): string {
                return match ($fieldHandle) {
                    'titleOrHonorific' => '',
                    default => throw new Exception(),
                };
            }
        );

        $speakerQuery = $this->createMock(
            EntryQuery::class,
        );

        $speakerQuery->method('all')->willReturn([
            $speaker1,
            $speaker2,
        ]);

        $sermonAudioAsset = $this->createMock(Asset::class);

        $sermonAudioAsset->method('getMimeType')->willReturn(
            'audio/mpeg',
        );

        $sermonAudioAsset->method('getExtension')->willReturn(
            'mp3',
        );

        $sermonAudioAsset->method('getUrl')->willReturn(
            '/test/asset/url',
        );

        $sermonAudioAssetQuery = $this->createMock(
            AssetQuery::class,
        );

        $sermonAudioAssetQuery->method('one')->willReturn(
            $sermonAudioAsset,
        );

        $sermon = $this->createMock(Entry::class);

        /** @phpstan-ignore-next-line */
        $sermon->postDate = DateTime::createFromFormat(
            DateTimeInterface::ATOM,
            '1982-01-27T00:00:00+00:00',
        );

        $series1 = $this->createMock(Category::class);

        $series1->title = 'Series Title 1';

        $series1->slug = 'series-slug-1';

        $series2 = $this->createMock(Category::class);

        $series2->title = 'Series Title 2';

        $series2->slug = 'series-slug-2';

        $seriesQuery = $this->createMock(
            CategoryQuery::class,
        );

        $seriesQuery->method('all')->willReturn([
            $series1,
            $series2,
        ]);

        $sermon->method('getFieldValue')->willReturnCallback(
            static function (string $fieldHandle) use (
                $speakerQuery,
                $sermonAudioAssetQuery,
                $seriesQuery,
            ): mixed {
                return match ($fieldHandle) {
                    'profile' => $speakerQuery,
                    'audio' => $sermonAudioAssetQuery,
                    'messageText' => 'Test Message Text',
                    'messageSeries' => $seriesQuery,
                    default => throw new Exception(),
                };
            }
        );

        $sermon->method('getUrl')->willReturn(
            '/test/sermon/url'
        );

        $sermon->title = 'Test Sermon Title';

        $factory = new AudioPlayerContentModelFactory();

        $model = $factory->makeFromSermonEntry(sermon: $sermon);

        self::assertSame(
            '/test/sermon/url',
            $model->href(),
        );

        self::assertSame(
            'Test Sermon Title',
            $model->title(),
        );

        self::assertSame(
            'January 27, 1982',
            $model->subTitle(),
        );

        self::assertSame(
            '/test/asset/url',
            $model->audioFileHref(),
        );

        self::assertSame(
            'audio/mp3',
            $model->audioFileMimeType(),
        );

        self::assertTrue($model->hasKeyValueItems());

        $keyValItems = $model->keyValueItems();

        self::assertCount(5, $keyValItems);

        $keyVal1 = $keyValItems[0];
        self::assertSame('by', $keyVal1->key());
        self::assertSame(
            'Test Title 1 Test Full Name 1',
            $keyVal1->value(),
        );
        self::assertSame(
            '/media/messages?by%5B0%5D=test-slug-1',
            $keyVal1->href(),
        );

        $keyVal2 = $keyValItems[1];
        self::assertSame('by', $keyVal2->key());
        self::assertSame(
            'Test Full Name 2',
            $keyVal2->value(),
        );
        self::assertSame(
            '/media/messages?by%5B0%5D=test-slug-2',
            $keyVal2->href(),
        );

        $keyVal3 = $keyValItems[2];
        self::assertSame('text', $keyVal3->key());
        self::assertSame(
            'Test Message Text',
            $keyVal3->value(),
        );
        self::assertSame(
            '',
            $keyVal3->href(),
        );

        $keyVal4 = $keyValItems[3];
        self::assertSame('series', $keyVal4->key());
        self::assertSame(
            'Series Title 1',
            $keyVal4->value(),
        );
        self::assertSame(
            '/media/messages?series%5B0%5D=series-slug-1',
            $keyVal4->href(),
        );

        $keyVal5 = $keyValItems[4];
        self::assertSame('series', $keyVal5->key());
        self::assertSame(
            'Series Title 2',
            $keyVal5->value(),
        );
        self::assertSame(
            '/media/messages?series%5B0%5D=series-slug-2',
            $keyVal5->href(),
        );

        self::assertSame(
            [
                $keyVal4,
                $keyVal5,
            ],
            $model->getSeries(),
        );

        self::assertSame(
            $keyVal4,
            $model->getFirstSeries(),
        );

        self::assertSame(
            $keyVal4,
            $model->firstSeriesGuarantee(),
        );
    }

    public function testGetFirstSeriesWhenNoSeries(): void
    {
        $model = new AudioPlayerContentModel(
            href: 'testHref',
            title: 'testTitle',
            subTitle: 'testSubTitle',
            audioFileHref: 'testAudioHref',
            audioFileMimeType: 'testMimeType',
            keyValueItems: [],
        );

        self::assertNull($model->getFirstSeries());
    }

    public function testMakeFromInternalMessageEntry(): void
    {
        $speaker1 = $this->createMock(Entry::class);

        $speaker1->slug = 'test-slug-1';

        $speaker1->title = 'Test Full Name 1';

        $speaker1->method('getFieldValue')->willReturnCallback(
            static function (string $fieldHandle): string {
                return match ($fieldHandle) {
                    'titleOrHonorific' => 'Test Title 1',
                    default => throw new Exception(),
                };
            }
        );

        $speaker2 = $this->createMock(Entry::class);

        $speaker2->slug = 'test-slug-2';

        $speaker2->title = 'Test Full Name 2';

        $speaker2->method('getFieldValue')->willReturnCallback(
            static function (string $fieldHandle): string {
                return match ($fieldHandle) {
                    'titleOrHonorific' => '',
                    default => throw new Exception(),
                };
            }
        );

        $speakerQuery = $this->createMock(
            EntryQuery::class,
        );

        $speakerQuery->method('all')->willReturn([
            $speaker1,
            $speaker2,
        ]);

        $sermonAudioAsset = $this->createMock(Asset::class);

        $sermonAudioAsset->method('getMimeType')->willReturn(
            'audio/mpeg',
        );

        $sermonAudioAsset->method('getExtension')->willReturn(
            'mp3',
        );

        $sermonAudioAsset->method('getUrl')->willReturn(
            '/test/asset/url',
        );

        $sermonAudioAssetQuery = $this->createMock(
            AssetQuery::class,
        );

        $sermonAudioAssetQuery->method('one')->willReturn(
            $sermonAudioAsset,
        );

        $sermon = $this->createMock(Entry::class);

        /** @phpstan-ignore-next-line */
        $sermon->postDate = DateTime::createFromFormat(
            DateTimeInterface::ATOM,
            '1982-01-27T00:00:00+00:00',
        );

        $sermon->method('getFieldValue')->willReturnCallback(
            static function (string $fieldHandle) use (
                $speakerQuery,
                $sermonAudioAssetQuery,
            ): mixed {
                return match ($fieldHandle) {
                    'profile' => $speakerQuery,
                    'internalAudio' => $sermonAudioAssetQuery,
                    'messageText' => 'Test Message Text',
                    default => throw new Exception(),
                };
            }
        );

        $sermon->method('getUrl')->willReturn(
            '/test/sermon/url'
        );

        $sermon->title = 'Test Sermon Title';

        $sermon->slug = 'test-slug';

        $factory = new AudioPlayerContentModelFactory();

        $model = $factory->makeFromInternalMessageEntry(entry: $sermon);

        self::assertSame(
            '/test/sermon/url',
            $model->href(),
        );

        self::assertSame(
            'Test Sermon Title',
            $model->title(),
        );

        self::assertSame(
            'January 27, 1982',
            $model->subTitle(),
        );

        self::assertSame(
            '/members/internal-audio/audio/test-slug',
            $model->audioFileHref(),
        );

        self::assertSame(
            'audio/mp3',
            $model->audioFileMimeType(),
        );

        self::assertTrue($model->hasKeyValueItems());

        $keyValItems = $model->keyValueItems();

        self::assertCount(3, $keyValItems);

        $keyVal1 = $keyValItems[0];
        self::assertSame('by', $keyVal1->key());
        self::assertSame(
            'Test Title 1 Test Full Name 1',
            $keyVal1->value(),
        );
        self::assertSame(
            '/media/messages?by%5B0%5D=test-slug-1',
            $keyVal1->href(),
        );

        $keyVal2 = $keyValItems[1];
        self::assertSame('by', $keyVal2->key());
        self::assertSame(
            'Test Full Name 2',
            $keyVal2->value(),
        );
        self::assertSame(
            '/media/messages?by%5B0%5D=test-slug-2',
            $keyVal2->href(),
        );

        $keyVal3 = $keyValItems[2];
        self::assertSame('text', $keyVal3->key());
        self::assertSame(
            'Test Message Text',
            $keyVal3->value(),
        );
        self::assertSame(
            '',
            $keyVal3->href(),
        );

        self::assertSame([], $model->getSeries());

        self::assertSame(null, $model->getFirstSeries());
    }
}
