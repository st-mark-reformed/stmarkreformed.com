<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsList;

use App\Http\Pagination\Pagination;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\EntryBuilder\ExtractBodyContent;
use App\Shared\Testing\TestCase;
use App\Shared\Utility\TruncateFactory;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use TS\Text\Truncation;
use yii\base\InvalidConfigException;

use function array_map;
use function assert;

class RetrieveNewsItemsTest extends TestCase
{
    private RetrieveNewsItems $retrieveNewsItems;

    protected function setUp(): void
    {
        parent::setUp();

        $this->retrieveNewsItems = new RetrieveNewsItems(
            truncateFactory: $this->mockTruncateFactory(),
            entryQueryFactory: $this->mockEntryQueryFactory(),
            extractBodyContent: $this->mockExtractBodyContent(),
        );
    }

    /**
     * @return TruncateFactory&MockObject
     */
    private function mockTruncateFactory(): TruncateFactory|MockObject
    {
        $truncation = $this->createMock(Truncation::class);

        $truncation->method('truncate')->willReturnCallback(
            function (): string {
                return $this->genericCall(
                    object: 'Truncation',
                    return: 'TruncatedString',
                );
            }
        );

        $truncateFactory = $this->createMock(
            TruncateFactory::class,
        );

        $truncateFactory->method('make')->willReturnCallback(
            function () use ($truncation): Truncation {
                return $this->genericCall(
                    object: 'TruncateFactory',
                    return: $truncation,
                );
            }
        );

        return $truncateFactory;
    }

    /**
     * @return EntryQueryFactory&MockObject
     */
    private function mockEntryQueryFactory(): EntryQueryFactory|MockObject
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
    private function mockEntryQuery(): EntryQuery|MockObject
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

        $entryQuery->method('count')->willReturnCallback(
            static function (): int {
                return 456;
            }
        );

        $entryQuery->method('limit')->willReturnCallback(
            $methodCallback
        );

        $entryQuery->method('offset')->willReturnCallback(
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
                    $this->mockEntry(
                        title: 'Entry Title 1',
                        url: '/entry/url/1',
                        dateTimeAtom: '1982-01-27T00:00:00+00:00',
                    ),
                    $this->mockEntry(
                        title: 'Entry Title 2',
                        url: '/entry/url/2',
                        dateTimeAtom: '1992-01-27T00:00:00+00:00',
                    ),
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
    private function mockEntry(
        string $title,
        string $url,
        string $dateTimeAtom,
    ): Entry|MockObject {
        $entry = $this->createMock(Entry::class);

        $entry->title = $title;

        $entry->method('getUrl')->willReturn($url);

        $dateTime = DateTime::createFromFormat(
            DateTimeInterface::ATOM,
            $dateTimeAtom,
        );

        assert($dateTime instanceof DateTime);

        $entry->postDate = $dateTime;

        return $entry;
    }

    /**
     * @return ExtractBodyContent&MockObject
     */
    private function mockExtractBodyContent(): ExtractBodyContent|MockObject
    {
        $extractor = $this->createMock(
            ExtractBodyContent::class,
        );

        $extractor->method('fromElementWithEntryBuilder')
            ->willReturnCallback(function (): string {
                return $this->genericCall(
                    object: 'ExtractBodyContent',
                    return: 'ExtractedBodyContent',
                );
            });

        return $extractor;
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testRetrieve(): void
    {
        $pagination = (new Pagination())->withPerPage(val: 987)
            ->withCurrentPage(val: 476);

        $results = $this->retrieveNewsItems->retrieve(pagination: $pagination);

        self::assertTrue($results->hasEntries());

        self::assertSame(456, $results->totalResults());

        self::assertSame(2, $results->count());

        self::assertSame(
            [
                [
                    'title' => 'Entry Title 1',
                    'excerpt' => 'TruncatedString',
                    'url' => '/entry/url/1',
                    'date' => 'January 27th, 1982',
                ],
                [
                    'title' => 'Entry Title 2',
                    'excerpt' => 'TruncatedString',
                    'url' => '/entry/url/2',
                    'date' => 'January 27th, 1992',
                ],
            ],
            array_map(
                static fn (NewsItem $item) => [
                    'title' => $item->title(),
                    'excerpt' => $item->excerpt(),
                    'url' => $item->url(),
                    'date' => $item->readableDate(),
                ],
                $results->items(),
            ),
        );

        self::assertCount(9, $this->calls);

        self::assertSame(
            [
                'object' => 'EntryQuery',
                'method' => 'section',
                'args' => ['news'],
            ],
            $this->calls[0],
        );

        self::assertSame(
            [
                'object' => 'EntryQuery',
                'method' => 'limit',
                'args' => [987],
            ],
            $this->calls[1],
        );

        self::assertSame(
            [
                'object' => 'EntryQuery',
                'method' => 'offset',
                'args' => [468825],
            ],
            $this->calls[2],
        );

        self::assertSame(
            [
                'object' => 'TruncateFactory',
                'method' => 'make',
                'args' => [300],
            ],
            $this->calls[3],
        );

        self::assertSame(
            'ExtractBodyContent',
            $this->calls[4]['object'],
        );

        self::assertSame(
            'fromElementWithEntryBuilder',
            $this->calls[4]['method'],
        );

        $call4Args = $this->calls[4]['args'];

        self::assertCount(1, $call4Args);

        $call4Entry = $call4Args[0];

        assert($call4Entry instanceof Entry);

        self::assertSame(
            '/entry/url/1',
            $call4Entry->getUrl(),
        );

        self::assertSame(
            [
                'object' => 'Truncation',
                'method' => 'truncate',
                'args' => ['ExtractedBodyContent'],
            ],
            $this->calls[5],
        );

        self::assertSame(
            [
                'object' => 'TruncateFactory',
                'method' => 'make',
                'args' => [300],
            ],
            $this->calls[6],
        );

        self::assertSame(
            'ExtractBodyContent',
            $this->calls[7]['object'],
        );

        self::assertSame(
            'fromElementWithEntryBuilder',
            $this->calls[7]['method'],
        );

        $call7Args = $this->calls[7]['args'];

        self::assertCount(1, $call7Args);

        $call7Entry = $call7Args[0];

        assert($call7Entry instanceof Entry);

        self::assertSame(
            '/entry/url/2',
            $call7Entry->getUrl(),
        );

        self::assertSame(
            [
                'object' => 'Truncation',
                'method' => 'truncate',
                'args' => ['ExtractedBodyContent'],
            ],
            $this->calls[8],
        );
    }
}
