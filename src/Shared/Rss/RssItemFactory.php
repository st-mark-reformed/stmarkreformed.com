<?php

/** @noinspection HttpUrlsUsage */

declare(strict_types=1);

namespace App\Shared\Rss;

use App\Craft\Behaviors\ProfileEntriesBehavior;
use App\Http\Utility\UrlHelper;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Categories\CategoriesFieldHandler;
use App\Shared\FieldHandlers\Entry\EntryFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use DateTimeInterface;
use SimpleXMLElement;
use yii\base\Exception;
use yii\base\InvalidConfigException;

use function assert;
use function htmlspecialchars;
use function implode;

// phpcs:disable SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration.MissingVariable

/**
 * TODO: Test this
 *
 * @codeCoverageIgnore
 */
class RssItemFactory
{
    public function __construct(
        private UrlHelper $urlHelper,
        private GenericHandler $genericHandler,
        private EntryFieldHandler $entryFieldHandler,
        private AssetsFieldHandler $assetsFieldHandler,
        private CategoriesFieldHandler $categoriesFieldHandler,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function fromSermonEntry(
        Entry $entry,
        SimpleXMLElement $channel,
        string $imageUrl = '',
    ): void {
        $item = $channel->addChild('item');

        $item->addChild(
            'title',
            htmlspecialchars((string) $entry->title),
        );

        $item->addChild(
            'link',
            $entry->getUrl()
        );

        $guid = $item->addChild(
            'guid',
            $entry->uid,
        );

        $guid->addAttribute(
            'isPermalink',
            'false',
        );

        $publishedAt = $entry->postDate;

        assert($publishedAt instanceof DateTimeInterface);

        $item->addChild(
            'pubDate',
            $publishedAt->format(DateTimeInterface::RFC2822),
        );

        $speaker = $this->entryFieldHandler->getOneOrNull(
            element: $entry,
            field: 'profile',
        );

        if ($speaker instanceof Entry) {
            /** @phpstan-ignore-next-line */
            /** @var Entry&ProfileEntriesBehavior $speaker */

            $authorString = htmlspecialchars(
                $speaker->fullNameHonorific()
            );

            $item->addChild(
                'author',
                $authorString,
            );

            $item->addChild(
                'itunes:author',
                $authorString,
                'http://www.itunes.com/dtds/podcast-1.0.dtd',
            );
        }

        $desc = $this->genericHandler->getString(
            element: $entry,
            field: 'shortDescription',
        );

        if ($desc !== '') {
            $desc = htmlspecialchars($desc);

            $item->addChild('description', $desc);

            $item->addChild(
                'itunes:summary',
                $desc,
                'http://www.itunes.com/dtds/podcast-1.0.dtd',
            );
        }

        $audio = $this->assetsFieldHandler->getOneOrNull(
            element: $entry,
            field: 'audio',
        );

        if ($audio instanceof Asset) {
            $enclosure = $item->addChild('enclosure');

            $enclosure->addAttribute(
                'url',
                (string) $audio->getUrl(),
            );

            $enclosure->addAttribute(
                'length',
                (string) $audio->size,
            );

            $enclosure->addAttribute(
                'type',
                (string) $audio->getMimeType(),
            );

            // Maybe TODO someday: calculate mp3 duration
            // $item->addChild(
            //     'itunes:duration',
            //     $episode->getFeedRunTime(),
            //     'http://www.itunes.com/dtds/podcast-1.0.dtd',
            // );
        }

        $sermonText = $this->genericHandler->getString(
            element: $entry,
            field: 'messageText',
        );

        $content = ['<ul>'];

        $content[] = '<li><strong>Title:</strong> ';

        $content[] = $entry->title . '</li>';

        $content[] = '<li><strong>Text:</strong> ';

        $content[] = $sermonText . '</li>';

        $series = $this->categoriesFieldHandler->getOneOrNull(
            element: $entry,
            field: 'messageSeries',
        );

        if ($series instanceof Category) {
            $content[] = '<li><strong>Series:</strong> ';

            $content[] = '<a href="';

            $content[] = $this->urlHelper->siteUrl(
                '/media/messages',
                [
                    'series' => [(string) $series->slug],
                ]
            );

            $content[] = '">';

            $content[] = $series->title . '</a></li>';
        }

        $content[] = '</ul>';

        $item->addChild(
            'content:encoded',
            htmlspecialchars(
                implode('', $content)
            ),
            'http://purl.org/rss/1.0/modules/content/',
        );

        if ($imageUrl === '') {
            return;
        }

        $image = $item->addChild('image');

        $image->addChild('url', $imageUrl);

        $itunesImage = $item->addChild(
            'itunes:image',
            '',
            'http://www.itunes.com/dtds/podcast-1.0.dtd',
        );

        $itunesImage->addAttribute('href', $imageUrl);
    }
}
