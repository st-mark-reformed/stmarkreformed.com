<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\Shared\AudioPlayer;

use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\db\AssetQuery;
use craft\elements\db\CategoryQuery;
use craft\elements\db\UserQuery;
use craft\elements\Entry;
use craft\elements\User;
use craft\errors\InvalidFieldException;
use DateTimeInterface;
use yii\base\InvalidConfigException;

use function assert;
use function implode;
use function trim;

class AudioPlayerContentModelFactory
{
    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function makeFromSermonEntry(Entry $sermon): AudioPlayerContentModel
    {
        $sermonPostDate = $sermon->postDate;

        assert($sermonPostDate instanceof DateTimeInterface);

        $sermonAudioAssetQuery = $sermon->getFieldValue('audio');

        assert($sermonAudioAssetQuery instanceof AssetQuery);

        $sermonAudioAsset = $sermonAudioAssetQuery->one();

        assert($sermonAudioAsset instanceof Asset);

        $mimeType = $sermonAudioAsset->getMimeType();

        if (
            $mimeType === 'audio/mpeg' &&
            $sermonAudioAsset->getExtension() === 'mp3'
        ) {
            $mimeType = 'audio/mp3';
        }

        $speakerQuery = $sermon->getFieldValue('speaker');

        assert($speakerQuery instanceof UserQuery);

        $keyValItems = [];

        foreach ($speakerQuery->all() as $speaker) {
            /** @phpstan-ignore-next-line */
            assert($speaker instanceof User);

            $slug = (string) $speaker->getFieldValue(
                'slugField'
            );

            $keyValItems[] = new AudioPlayerKeyValItem(
                key: 'by',
                value: trim(implode(' ', [
                    (string) $speaker->getFieldValue(
                        'titleOrHonorific',
                    ),
                    (string) $speaker->getFullName(),
                ])),
                href: '/media/messages/by/' . $slug,
            );
        }

        $keyValItems[] = new AudioPlayerKeyValItem(
            key: 'text',
            value: (string) $sermon->getFieldValue('messageText'),
        );

        $seriesQuery = $sermon->getFieldValue('messageSeries');

        assert($seriesQuery instanceof CategoryQuery);

        $series = $seriesQuery->all();

        foreach ($series as $seriesCategory) {
            /** @phpstan-ignore-next-line */
            assert($seriesCategory instanceof Category);

            $slug = (string) $seriesCategory->slug;

            $keyValItems[] = new AudioPlayerKeyValItem(
                key: 'series',
                value: (string) $seriesCategory->title,
                href: '/media/messages/series/' . $slug,
            );
        }

        return new AudioPlayerContentModel(
            href: (string) $sermon->getUrl(),
            title: (string) $sermon->title,
            subTitle: $sermonPostDate->format('F j, Y'),
            audioFileHref: (string) $sermonAudioAsset->getUrl(),
            audioFileMimeType: (string) $mimeType,
            keyValueItems: $keyValItems,
        );
    }
}
