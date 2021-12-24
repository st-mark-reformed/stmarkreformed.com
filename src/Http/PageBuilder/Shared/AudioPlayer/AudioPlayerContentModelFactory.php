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
use DateTimeInterface;
use yii\base\InvalidConfigException;

use function assert;
use function http_build_query;
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

        $speakerQuery = $sermon->getFieldValue('profile');

        assert($speakerQuery instanceof EntryQuery);

        $keyValItems = [];

        foreach ($speakerQuery->all() as $speaker) {
            /** @phpstan-ignore-next-line */
            assert($speaker instanceof Entry);

            $slug = (string) $speaker->slug;

            $keyValItems[] = new AudioPlayerKeyValItem(
                key: 'by',
                value: trim(implode(' ', [
                    (string) $speaker->getFieldValue(
                        'titleOrHonorific',
                    ),
                    (string) $speaker->title,
                ])),
                href: '/media/messages?' . http_build_query(
                    ['by' => [$slug]],
                ),
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
                href: '/media/messages?' . http_build_query(
                    ['series' => [$slug]],
                ),
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

    /**
     * @throws InvalidFieldException
     */
    public function makeFromInternalMessageEntry(
        Entry $entry,
    ): AudioPlayerContentModel {
        $postDate = $entry->postDate;

        assert($postDate instanceof DateTimeInterface);

        $audioAssetQuery = $entry->getFieldValue('internalAudio');

        assert($audioAssetQuery instanceof AssetQuery);

        $audioAsset = $audioAssetQuery->one();

        assert($audioAsset instanceof Asset);

        $mimeType = $audioAsset->getMimeType();

        if (
            $mimeType === 'audio/mpeg' &&
            $audioAsset->getExtension() === 'mp3'
        ) {
            $mimeType = 'audio/mp3';
        }

        $speakerQuery = $entry->getFieldValue('profile');

        assert($speakerQuery instanceof EntryQuery);

        $keyValItems = [];

        foreach ($speakerQuery->all() as $speaker) {
            /** @phpstan-ignore-next-line */
            assert($speaker instanceof Entry);

            $slug = (string) $speaker->slug;

            $keyValItems[] = new AudioPlayerKeyValItem(
                key: 'by',
                value: trim(implode(' ', [
                    (string) $speaker->getFieldValue(
                        'titleOrHonorific',
                    ),
                    (string) $speaker->title,
                ])),
                href: '/media/messages?' . http_build_query(
                    ['by' => [$slug]],
                ),
            );
        }

        $messageText = (string) $entry->getFieldValue(
            'messageText',
        );

        if ($messageText !== '') {
            $keyValItems[] = new AudioPlayerKeyValItem(
                key: 'text',
                value: $messageText,
            );
        }

        $slug = (string) $entry->slug;

        return new AudioPlayerContentModel(
            href: (string) $entry->getUrl(),
            title: (string) $entry->title,
            subTitle: $postDate->format('F j, Y'),
            audioFileHref: '/members/internal-audio/audio/' . $slug,
            audioFileMimeType: (string) $mimeType,
            keyValueItems: $keyValItems,
        );
    }
}
