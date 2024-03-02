<?php

/** @noinspection HttpUrlsUsage */

declare(strict_types=1);

namespace App\Shared\Rss;

use DateTimeImmutable;
use DateTimeInterface;
use SimpleXMLElement;

use function count;
use function htmlspecialchars;
use function implode;

class RssChannelFactory
{
    /**
     * @param string[] $keywords
     */
    public function makePodcastChannel(
        SimpleXMLElement $feed,
        string $publicFeedUrl,
        string $feedTitle,
        DateTimeInterface $lastPubDate,
        string $feedDescription,
        string $itunesAuthor,
        string $imageUrl,
        string $ownerName,
        string $ownerEmail,
        string $category,
        array $keywords = [],
    ): SimpleXMLElement {
        $channel = $feed->addChild('channel');

        $atomLink = $channel->addChild(
            'atom:link',
            '',
            'http://www.w3.org/2005/Atom/'
        );

        $atomLink->addAttribute('href', $publicFeedUrl);

        $atomLink->addAttribute(
            'type',
            'application/rss+xml',
        );

        $atomLink->addAttribute('rel', 'self');

        $channel->addChild('title', $feedTitle);

        $channel->addChild('link', $publicFeedUrl);

        $channel->addChild(
            'pubDate',
            $lastPubDate->format(DateTimeInterface::RFC2822),
        );

        $desc = htmlspecialchars($feedDescription);

        $channel->addChild('description', $desc);

        $channel->addChild(
            'itunes:summary',
            $desc,
            'http://www.itunes.com/dtds/podcast-1.0.dtd',
        );

        $channel->addChild('language', 'en-US');

        $channel->addChild(
            'itunes:author',
            htmlspecialchars($itunesAuthor),
            'http://www.itunes.com/dtds/podcast-1.0.dtd',
        );

        $image = $channel->addChild('image');

        $image->addChild('url', $imageUrl);

        $itunesImage = $channel->addChild(
            'itunes:image',
            '',
            'http://www.itunes.com/dtds/podcast-1.0.dtd',
        );

        $itunesImage->addAttribute('href', $imageUrl);

        $owner = $channel->addChild(
            'itunes:owner',
            '',
            'http://www.itunes.com/dtds/podcast-1.0.dtd',
        );

        $owner->addChild(
            'itunes:name',
            $ownerName,
            'http://www.itunes.com/dtds/podcast-1.0.dtd',
        );

        $owner->addChild(
            'itunes:email',
            $ownerEmail,
            'http://www.itunes.com/dtds/podcast-1.0.dtd',
        );

        $categoryElement = $channel->addChild(
            'itunes:category',
            '',
            'http://www.itunes.com/dtds/podcast-1.0.dtd',
        );

        $categoryElement->addAttribute(
            'text',
            $category,
        );

        // @codeCoverageIgnoreStart
        if (count($keywords) > 0) {
            $channel->addChild(
                'itunes:keywords',
                implode(', ', $keywords),
                'http://www.itunes.com/dtds/podcast-1.0.dtd',
            );
        }

        // @codeCoverageIgnoreEnd

        $channel->addChild(
            'itunes:explicit',
            'no',
            'http://www.itunes.com/dtds/podcast-1.0.dtd',
        );

        $channel->addChild(
            'copyright',
            'Copyright ' .
            (new DateTimeImmutable())->format('Y') .
            ' St. Mark Reformed Church'
        );

        return $channel;
    }

    public function makeBlogChannel(
        SimpleXMLElement $feed,
        string $title,
        string $description,
        string $link,
        string $feedUrl,
    ): SimpleXMLElement {
        $channel = $feed->addChild('channel');

        $channel->addChild('title', $title);

        $channel->addChild('description', $description);

        $channel->addChild('link', $link);

        $channel->addChild('language', 'en-us');

        $atomLink = $channel->addChild(
            'atom:link',
            '',
            'http://www.w3.org/2005/Atom',
        );

        $atomLink->addAttribute('href', $feedUrl);

        $atomLink->addAttribute('rel', 'self');

        return $channel;
    }
}
