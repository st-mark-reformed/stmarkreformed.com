<?php

declare(strict_types=1);

namespace App\Http\Response\Media\MessagesFeed;

use App\Http\Utility\UrlHelper;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\Rss\RssChannelFactory;
use App\Shared\Rss\RssFeedFactory;
use App\Shared\Rss\RssItemFactory;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use yii\base\Exception;
use yii\base\InvalidConfigException;

use function array_map;
use function assert;
use function dom_import_simplexml;

class MessagesRssFeedFactory
{
    public function __construct(
        private UrlHelper $urlHelper,
        private RssItemFactory $rssItemFactory,
        private RssFeedFactory $rssFeedFactory,
        private EntryQueryFactory $queryFactory,
        private RssChannelFactory $rssChannelFactory,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function make(): DOMDocument
    {
        /** @var Entry[] $entries */
        $entries = $this->queryFactory->make()->section('messages')
            ->limit(100)
            ->all();

        $lastPostDate = $entries[0]->postDate;

        assert($lastPostDate instanceof DateTime);

        $lastPubDate = DateTimeImmutable::createFromMutable(
            $lastPostDate
        )->setTimezone(new DateTimeZone('UTC'));

        $feed = $this->rssFeedFactory->makePodcastFeed();

        $channel = $this->rssChannelFactory->makePodcastChannel(
            feed: $feed,
            publicFeedUrl: $this->urlHelper->siteUrl(
                '/media/messages/feed',
            ),
            feedTitle: 'Messages From St. Mark Reformed Church',
            lastPubDate: $lastPubDate,
            feedDescription: 'Serving Christ and the world through ' .
                'liturgy, mission, and community.',
            itunesAuthor: 'St. Mark Reformed Church',
            imageUrl: $this->urlHelper->siteUrl(
                path: '/assets/img/st-mark-podcast-art.jpg',
            ),
            ownerName: 'St. Mark Reformed Church',
            ownerEmail: 'info@stmarkreformed.com',
            category: 'Religion & Spirituality',
        );

        array_map(
            function (Entry $entry) use ($channel): void {
                $this->rssItemFactory->fromSermonEntry(
                    entry: $entry,
                    channel: $channel,
                    imageUrl: $this->urlHelper->siteUrl(
                        path: '/assets/img/st-mark-podcast-art.jpg',
                    ),
                );
            },
            $entries,
        );

        $domElement = dom_import_simplexml($feed);

        $dom = new DOMDocument('1.0', 'UTF-8');

        $dom->appendChild(
            $dom->importNode($domElement, true)
        );

        $dom->formatOutput = true;

        return $dom;
    }
}
