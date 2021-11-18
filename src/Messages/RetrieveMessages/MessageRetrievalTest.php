<?php

declare(strict_types=1);

namespace App\Messages\RetrieveMessages;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function debug_backtrace;

class MessageRetrievalTest extends TestCase
{
    private MessageRetrieval $messageRetrieval;

    /** @var mixed[] */
    private array $calls = [];

    /** @var string[] */
    private array $elasticUids = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->elasticUids = [];

        $this->messageRetrieval = new MessageRetrieval(
            queryFactory: $this->mockEntryQueryFactory(),
            elasticUidRetrieval: $this->mockElasticUidRetrieval(),
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

        $callable = function (mixed $value) use ($entryQuery): mixed {
            $this->calls[] = [
                'object' => 'EntryQuery',
                'method' => debug_backtrace()[4]['function'],
                'value' => $value,
            ];

            return $entryQuery;
        };

        $entryQuery->method('section')->willReturnCallback(
            $callable,
        );

        $entryQuery->method('uid')->willReturnCallback(
            $callable,
        );

        $entryQuery->method('postDate')->willReturnCallback(
            $callable,
        );

        $entryQuery->method('limit')->willReturnCallback(
            $callable,
        );

        $entryQuery->method('offset')->willReturnCallback(
            $callable,
        );

        $entryQuery->method('count')->willReturn(42);

        $entry1 = $this->createMock(Entry::class);

        $entry1->slug = 'entry-1';

        $entry2 = $this->createMock(Entry::class);

        $entry2->slug = 'entry-2';

        $entryQuery->method('all')->willReturn([
            $entry1,
            $entry2,
        ]);

        return $entryQuery;
    }

    /**
     * @return ElasticUidRetrieval&MockObject
     */
    private function mockElasticUidRetrieval(): mixed
    {
        $elasticUidRetrieval = $this->createMock(
            ElasticUidRetrieval::class,
        );

        $elasticUidRetrieval->method('fromParams')
            ->willReturnCallback(
                function (MessageRetrievalParams $params): array {
                    $this->calls[] = [
                        'object' => 'ElasticUidRetrieval',
                        'method' => 'fromParams',
                        'params' => $params,
                    ];

                    return $this->elasticUids;
                }
            );

        return $elasticUidRetrieval;
    }

    public function testWithNoUidsAndStartAndEndDate(): void
    {
        $params = new MessageRetrievalParams(
            limit: 38,
            offset: 23,
            /** @phpstan-ignore-next-line */
            dateRangeStart: DateTimeImmutable::createFromFormat(
                'Y-m-d\TH:i:sP',
                '1982-01-27T10:30:00-06:00',
            ),
            /** @phpstan-ignore-next-line */
            dateRangeEnd: DateTimeImmutable::createFromFormat(
                'Y-m-d\TH:i:sP',
                '1992-01-27T10:30:00-06:00',
            ),
        );

        $result = $this->messageRetrieval->fromParams(params: $params);

        self::assertSame(42, $result->absoluteTotal());

        self::assertCount(2, $result->messages());

        self::assertSame(2, $result->count());

        self::assertSame(
            'entry-1',
            $result->messages()[0]->slug,
        );

        self::assertSame(
            'entry-2',
            $result->messages()[1]->slug,
        );

        self::assertSame(
            [
                [
                    'object' => 'ElasticUidRetrieval',
                    'method' => 'fromParams',
                    'params' => $params,
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'value' => 'messages',
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'postDate',
                    'value' => [
                        'and',
                        '>= 1982-01-27',
                        '<= 1992-01-27',
                    ],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'limit',
                    'value' => 38,
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'offset',
                    'value' => 23,
                ],
            ],
            $this->calls,
        );
    }

    public function testWithUidsAndStartDate(): void
    {
        $this->elasticUids = [
            'fooBarUid1',
            'fooBarUid2',
        ];

        $params = new MessageRetrievalParams(
            limit: 19,
            offset: 5,
            /** @phpstan-ignore-next-line */
            dateRangeStart: DateTimeImmutable::createFromFormat(
                'Y-m-d\TH:i:sP',
                '1982-01-27T10:30:00-06:00',
            ),
        );

        $result = $this->messageRetrieval->fromParams(params: $params);

        self::assertSame(42, $result->absoluteTotal());

        self::assertCount(2, $result->messages());

        self::assertSame(2, $result->count());

        self::assertSame(
            'entry-1',
            $result->messages()[0]->slug,
        );

        self::assertSame(
            'entry-2',
            $result->messages()[1]->slug,
        );

        self::assertSame(
            [
                [
                    'object' => 'ElasticUidRetrieval',
                    'method' => 'fromParams',
                    'params' => $params,
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'value' => 'messages',
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'uid',
                    'value' => [
                        'fooBarUid1',
                        'fooBarUid2',
                    ],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'postDate',
                    'value' => '>= 1982-01-27',
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'limit',
                    'value' => 19,
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'offset',
                    'value' => 5,
                ],
            ],
            $this->calls,
        );
    }

    public function testWithEndDate(): void
    {
        $params = new MessageRetrievalParams(
            limit: 3,
            offset: 8,
            /** @phpstan-ignore-next-line */
            dateRangeEnd: DateTimeImmutable::createFromFormat(
                'Y-m-d\TH:i:sP',
                '1992-01-27T10:30:00-06:00',
            ),
        );

        $result = $this->messageRetrieval->fromParams(params: $params);

        self::assertSame(42, $result->absoluteTotal());

        self::assertCount(2, $result->messages());

        self::assertSame(2, $result->count());

        self::assertSame(
            'entry-1',
            $result->messages()[0]->slug,
        );

        self::assertSame(
            'entry-2',
            $result->messages()[1]->slug,
        );

        self::assertSame(
            [
                [
                    'object' => 'ElasticUidRetrieval',
                    'method' => 'fromParams',
                    'params' => $params,
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'value' => 'messages',
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'postDate',
                    'value' => '<= 1992-01-27',
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'limit',
                    'value' => 3,
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'offset',
                    'value' => 8,
                ],
            ],
            $this->calls,
        );
    }
}
