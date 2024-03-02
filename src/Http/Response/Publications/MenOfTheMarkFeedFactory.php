<?php

declare(strict_types=1);

namespace App\Http\Response\Publications;

use App\Http\Utility\UrlHelper;
use App\Shared\Rss\RssChannelFactory;
use App\Shared\Rss\RssFeedFactory;
use DateTimeInterface;
use DOMDocument;

use function dom_import_simplexml;
use function htmlspecialchars;

class MenOfTheMarkFeedFactory
{
    public function __construct(
        private UrlHelper $urlHelper,
        private RssFeedFactory $rssFeedFactory,
        private FetchMenOfTheMark $fetchMenOfTheMark,
        private RssChannelFactory $rssChannelFactory,
    ) {
    }

    public function make(): DOMDocument
    {
        $publications = $this->fetchMenOfTheMark->fetch();

        $feed = $this->rssFeedFactory->makeBlogFeed();

        $channel = $this->rssChannelFactory->makeBlogChannel(
            $feed,
            'Men of the Mark Publications',
            MenOfTheMarkMeta::DESCRIPTION,
            $this->urlHelper->siteUrl(
                '/publications/men-of-the-mark',
            ),
            $this->urlHelper->siteUrl(
                '/publications/men-of-the-mark/rss',
            ),
        );

        $publications->walk(
            static function (Publication $publication) use ($channel): void {
                $item = $channel->addChild('item');

                $item->addChild(
                    'title',
                    htmlspecialchars($publication->title),
                );

                $item->addChild(
                    'link',
                    $publication->url,
                );

                $guid = $item->addChild(
                    'guid',
                    $publication->uid,
                );

                $guid->addAttribute(
                    'isPermaLink',
                    'false',
                );

                $item->addChild(
                    'description',
                    htmlspecialchars($publication->title),
                );

                $item->addChild(
                    'pubDate',
                    $publication->publicationDate->format(
                        DateTimeInterface::RFC2822,
                    ),
                );

                $item->addChild(
                    'content:encoded',
                    htmlspecialchars($publication->bodyHtml),
                    'http://purl.org/rss/1.0/modules/content/',
                );
            }
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
