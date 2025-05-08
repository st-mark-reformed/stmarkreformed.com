<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\Testing\TestCase;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;

use function assert;

// phpcs:disable Generic.Files.LineLength.TooLong

class RetrieveHymnsTest extends TestCase
{
    private RetrieveHymns $retrieveHymns;

    protected function setUp(): void
    {
        parent::setUp();

        $this->retrieveHymns = new RetrieveHymns(
            genericHandler: $this->mockGenericHandler(),
            entryQueryFactory: $this->mockEntryQueryFactory(),
        );
    }

    private function mockGenericHandler(): GenericHandler
    {
        $mock = $this->createMock(GenericHandler::class);

        $mock->method('getString')->willReturnCallback(
            function (): string {
                return $this->genericCall(
                    object: 'GenericHandler',
                    return: 'generic-handler-get-string-return',
                );
            }
        );

        return $mock;
    }

    private function mockEntryQueryFactory(): EntryQueryFactory
    {
        $mock = $this->createMock(EntryQueryFactory::class);

        $mock->method('make')->willReturn(
            $this->mockEntryQuery(),
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

        $mock->method('count')->willReturn('495');

        $mock->method('all')->willReturn([
            $this->mockEntry(
                url: '/foo/url/1',
                title: 'Foo Title 1',
            ),
            $this->mockEntry(
                url: '/foo/url/2',
                title: 'Foo Title 2',
            ),
        ]);

        return $mock;
    }

    /**
     * @phpstan-ignore-next-line
     */
    private function mockEntry(
        string $url,
        string $title,
    ): Entry {
        $mock = $this->createMock(Entry::class);

        $mock->method('getUrl')->willReturn($url);

        $mock->title = $title;

        return $mock;
    }

    /**
     * @throws InvalidFieldException
     */
    public function testRetrieve(): void
    {
        $results = $this->retrieveHymns->retrieve();

        self::assertTrue($results->hasResults());

        self::assertSame(495, $results->totalResults());

        self::assertSame(
            [
                [
                    'href' => '/foo/url/1',
                    'title' => 'Foo Title 1',
                    'content' => 'Resources and tools for learning the hymn of the month: generic-handler-get-string-return',
                ],
                [
                    'href' => '/foo/url/2',
                    'title' => 'Foo Title 2',
                    'content' => 'Resources and tools for learning the hymn of the month: generic-handler-get-string-return',
                ],
            ],
            $results->mapItems(static fn (HymnItem $item) => [
                'href' => $item->href(),
                'title' => $item->title(),
                'content' => $item->content(),
            ]),
        );

        self::assertCount(3, $this->calls);

        self::assertSame(
            [
                'object' => 'EntryQuery',
                'method' => 'section',
                'args' => ['hymnsOfTheMonth'],
            ],
            $this->calls[0],
        );

        self::assertSame(
            'GenericHandler',
            $this->calls[1]['object'],
        );

        self::assertSame(
            'getString',
            $this->calls[1]['method'],
        );

        $call1Args = $this->calls[1]['args'];

        self::assertCount(2, $call1Args);

        $call1Entry = $call1Args[0];

        assert($call1Entry instanceof Entry);

        self::assertSame(
            '/foo/url/1',
            $call1Entry->getUrl(),
        );

        self::assertSame('hymnPsalmName', $call1Args[1]);

        self::assertSame(
            'GenericHandler',
            $this->calls[2]['object'],
        );

        self::assertSame(
            'getString',
            $this->calls[2]['method'],
        );

        $call2Args = $this->calls[2]['args'];

        self::assertCount(2, $call2Args);

        $call2Entry = $call2Args[0];

        assert($call2Entry instanceof Entry);

        self::assertSame(
            '/foo/url/2',
            $call2Entry->getUrl(),
        );

        self::assertSame('hymnPsalmName', $call2Args[1]);
    }
}
