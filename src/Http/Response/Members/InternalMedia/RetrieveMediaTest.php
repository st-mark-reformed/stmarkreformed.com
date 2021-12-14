<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia;

use App\Http\Pagination\Pagination;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\Testing\TestCase;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;

class RetrieveMediaTest extends TestCase
{
    private Entry $entry1;

    private Entry $entry2;

    private Pagination $pagination;

    private RetrieveMedia $retrieveMedia;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entry1 = $this->createMock(Entry::class);

        $this->entry2 = $this->createMock(Entry::class);

        $this->pagination = (new Pagination())
            ->withPerPage(2)
            ->withCurrentPage(5);

        $this->retrieveMedia = new RetrieveMedia(
            entryQueryFactory: $this->mockEntryQueryFactory(),
        );
    }

    private function mockEntryQueryFactory(): EntryQueryFactory
    {
        $mock = $this->createMock(EntryQueryFactory::class);

        $mock->method('make')->willReturnCallback(
            function (): EntryQuery {
                return $this->mockEntryQuery();
            }
        );

        return $mock;
    }

    /**
     * @phpstan-ignore-next-line
     */
    private function mockEntryQuery(): EntryQuery
    {
        $mock = $this->createMock(EntryQuery::class);

        $mock->method('section')->willReturnCallback(
            function () use ($mock): EntryQuery {
                return $this->genericCall(
                    object: 'EntryQuery',
                    return: $mock,
                );
            }
        );

        $mock->method('count')->willReturn(536);

        $mock->method('limit')->willReturnCallback(
            function () use ($mock): EntryQuery {
                return $this->genericCall(
                    object: 'EntryQuery',
                    return: $mock,
                );
            }
        );

        $mock->method('offset')->willReturnCallback(
            function () use ($mock): EntryQuery {
                return $this->genericCall(
                    object: 'EntryQuery',
                    return: $mock,
                );
            }
        );

        $mock->method('all')->willReturnCallback(
            function (): array {
                return $this->genericCall(
                    object: 'EntryQuery',
                    return: [$this->entry1, $this->entry2],
                );
            }
        );

        return $mock;
    }

    public function testRetrieve(): void
    {
        $results = $this->retrieveMedia->retrieve(
            pagination: $this->pagination,
        );

        self::assertTrue($results->hasEntries());

        self::assertSame(536, $results->totalResults());

        self::assertSame(
            [$this->entry1, $this->entry2],
            $results->mapItems(static fn (Entry $e) => $e),
        );

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['internalMessages'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'limit',
                    'args' => [2],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'offset',
                    'args' => [8],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'all',
                    'args' => [],
                ],
            ],
            $this->calls,
        );
    }
}
