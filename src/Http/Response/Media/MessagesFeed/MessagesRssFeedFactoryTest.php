<?php

declare(strict_types=1);

namespace App\Http\Response\Media\MessagesFeed;

use App\Http\Utility\MockUrlHelperForTesting;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\Rss\MockRssItemFactoryForTesting;
use App\Shared\Rss\RssChannelFactory;
use App\Shared\Rss\RssFeedFactory;
use App\Shared\Testing\TestCase;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use SimpleXMLElement;
use yii\base\Exception;
use yii\base\InvalidConfigException;

use function assert;
use function file_get_contents;
use function str_replace;

// phpcs:disable Generic.Files.LineLength.TooLong

class MessagesRssFeedFactoryTest extends TestCase
{
    use MockUrlHelperForTesting;
    use MockRssItemFactoryForTesting;

    private MessagesRssFeedFactory $messagesRssFeedFactory;

    /** @var array<array-key, Entry&MockObject> */
    private array $entries = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->entries = [];

        $this->messagesRssFeedFactory = new MessagesRssFeedFactory(
            urlHelper: $this->mockUrlHelper(),
            rssItemFactory: $this->mockRssItemFactory(),
            rssFeedFactory: new RssFeedFactory(),
            queryFactory: $this->mockEntryQueryFactory(),
            rssChannelFactory: new RssChannelFactory(),
        );
    }

    /**
     * @return EntryQueryFactory&MockObject
     */
    private function mockEntryQueryFactory(): mixed
    {
        $entryQueryFactory = $this->createMock(
            EntryQueryFactory::class,
        );

        $entryQueryFactory->method('make')->willReturn(
            $this->mockEntryQuery(),
        );

        return $entryQueryFactory;
    }

    /**
     * @return EntryQuery&MockObject
     *
     * @phpstan-ignore-next-line
     */
    private function mockEntryQuery(): mixed
    {
        $entryQuery = $this->createMock(EntryQuery::class);

        $methodCallback = function () use ($entryQuery): EntryQuery {
            return $this->genericCall(
                object: 'EntryQuery',
                return: $entryQuery,
            );
        };

        $entryQuery->method('section')->willReturnCallback(
            $methodCallback
        );

        $entryQuery->method('limit')->willReturnCallback(
            $methodCallback
        );

        $entryQuery->method('all')->willReturnCallback(
            function (): array {
                $date1 = DateTime::createFromFormat(
                    DateTimeInterface::ATOM,
                    '1982-01-27T10:00:10+00:00'
                );

                assert($date1 instanceof DateTime);

                $date2 = DateTime::createFromFormat(
                    DateTimeInterface::ATOM,
                    '1972-01-27T10:00:10+00:00'
                );

                assert($date2 instanceof DateTime);

                return [
                    $this->mockEntry(postDate: $date1),
                    $this->mockEntry(postDate: $date2),
                ];
            }
        );

        return $entryQuery;
    }

    /**
     * @return Entry&MockObject
     *
     * @phpstan-ignore-next-line
     */
    private function mockEntry(DateTime $postDate): mixed
    {
        $entry = $this->createMock(Entry::class);

        $entry->postDate = $postDate;

        $this->entries[] = $entry;

        return $entry;
    }

    /**
     * @throws InvalidFieldException
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function testMake(): void
    {
        $feed = (string) file_get_contents(
            __DIR__ . '/RssFeedContentForTest.txt'
        );

        $feed = str_replace(
            '{{YEAR}}',
            (new DateTimeImmutable())->format('Y'),
            $feed,
        );

        $rssFeedDoc = $this->messagesRssFeedFactory->make();

        self::assertSame(
            $feed,
            $rssFeedDoc->saveXML(),
        );

        self::assertCount(8, $this->calls);

        $call0 = $this->calls[0];
        $call1 = $this->calls[1];
        $call2 = $this->calls[2];
        $call3 = $this->calls[3];
        $call4 = $this->calls[4];
        $call5 = $this->calls[5];
        $call6 = $this->calls[6];
        $call7 = $this->calls[7];

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['messages'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'limit',
                    'args' => [100],
                ],
                [
                    'object' => 'UrlHelper',
                    'method' => 'siteUrl',
                    'args' => ['/media/messages/feed'],
                ],
                [
                    'object' => 'UrlHelper',
                    'method' => 'siteUrl',
                    'args' => ['/assets/img/st-mark-podcast-art.jpg'],
                ],
                [
                    'object' => 'UrlHelper',
                    'method' => 'siteUrl',
                    'args' => ['/assets/img/st-mark-podcast-art.jpg'],
                ],
                [
                    'object' => 'UrlHelper',
                    'method' => 'siteUrl',
                    'args' => ['/assets/img/st-mark-podcast-art.jpg'],
                ],
            ],
            [
                $call0,
                $call1,
                $call2,
                $call3,
                $call4,
                $call6,
            ],
        );

        self::assertSame('RssItemFactory', $call5['object']);

        self::assertSame('fromSermonEntry', $call5['method']);

        $call5Args = $call5['args'];

        self::assertSame(
            $this->entries[0],
            $call5Args[0],
        );

        $call5Arg1 = $call5Args[1];

        assert($call5Arg1 instanceof SimpleXMLElement);

        self::assertSame(
            '<channel><atom:link href="TestSiteUrlReturn" type="application/rss+xml" rel="self"/><title>Messages From St. Mark Reformed Church</title><link>TestSiteUrlReturn</link><pubDate>Wed, 27 Jan 1982 10:00:10 +0000</pubDate><description>Serving Christ and the world through liturgy, mission, and community.</description><itunes:summary>Serving Christ and the world through liturgy, mission, and community.</itunes:summary><language>en-US</language><itunes:author>St. Mark Reformed Church</itunes:author><image><url>TestSiteUrlReturn</url></image><itunes:image href="TestSiteUrlReturn"/><itunes:owner><itunes:name>St. Mark Reformed Church</itunes:name><itunes:email>info@stmarkreformed.com</itunes:email></itunes:owner><itunes:category text="Religion &amp;amp; Spirituality"/><itunes:explicit>no</itunes:explicit><copyright>Copyright 2021 St. Mark Reformed Church</copyright></channel>',
            $call5Arg1->saveXML(),
        );

        self::assertSame(
            'TestSiteUrlReturn',
            $call5Args[2],
        );

        self::assertSame('RssItemFactory', $call7['object']);

        self::assertSame('fromSermonEntry', $call7['method']);

        $call7Args = $call7['args'];

        self::assertSame(
            $this->entries[1],
            $call7Args[0],
        );

        $call7Arg1 = $call7Args[1];

        assert($call7Arg1 instanceof SimpleXMLElement);

        self::assertSame(
            '<channel><atom:link href="TestSiteUrlReturn" type="application/rss+xml" rel="self"/><title>Messages From St. Mark Reformed Church</title><link>TestSiteUrlReturn</link><pubDate>Wed, 27 Jan 1982 10:00:10 +0000</pubDate><description>Serving Christ and the world through liturgy, mission, and community.</description><itunes:summary>Serving Christ and the world through liturgy, mission, and community.</itunes:summary><language>en-US</language><itunes:author>St. Mark Reformed Church</itunes:author><image><url>TestSiteUrlReturn</url></image><itunes:image href="TestSiteUrlReturn"/><itunes:owner><itunes:name>St. Mark Reformed Church</itunes:name><itunes:email>info@stmarkreformed.com</itunes:email></itunes:owner><itunes:category text="Religion &amp;amp; Spirituality"/><itunes:explicit>no</itunes:explicit><copyright>Copyright 2021 St. Mark Reformed Church</copyright></channel>',
            $call7Arg1->saveXML(),
        );

        self::assertSame(
            'TestSiteUrlReturn',
            $call7Args[2],
        );
    }
}
