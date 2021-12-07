<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries;

use App\Http\Pagination\Pagination;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\Testing\TestCase;
use craft\base\Element;
use craft\elements\Asset;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use yii\base\InvalidConfigException;

use function array_map;
use function assert;

class RetrieveGalleriesTest extends TestCase
{
    private RetrieveGalleries $retrieveGalleries;

    /** @var array<array-key, Entry&MockObject> */
    private array $entries = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->entries = [];

        $this->retrieveGalleries = new RetrieveGalleries(
            entryQueryFactory: $this->mockEntryQueryFactory(),
            assetsFieldHandler: $this->mockAssetFieldHandler(),
        );
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
                    ),
                    $this->mockEntry(
                        title: 'Entry Title 1',
                        url: '/entry/url/1',
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
    ): Entry|MockObject {
        $entry = $this->createMock(Entry::class);

        $entry->title = $title;

        $entry->method('getUrl')->willReturn($url);

        $this->entries[] = $entry;

        return $entry;
    }

    /**
     * @return AssetsFieldHandler&MockObject
     */
    private function mockAssetFieldHandler(): AssetsFieldHandler|MockObject
    {
        $handler = $this->createMock(
            AssetsFieldHandler::class,
        );

        $handler->method('getOne')->willReturnCallback(
            function (Element $element, string $field): Asset {
                $asset = $this->createMock(Asset::class);

                $asset->method('getUrl')->willReturn(
                    '/asset-url/' . $field . $element->getUrl()
                );

                return $this->genericCall(
                    object: 'AssetsFieldHandler',
                    return: $asset,
                );
            }
        );

        return $handler;
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testRetrieve(): void
    {
        $pagination = (new Pagination())->withPerPage(val: 987)
            ->withCurrentPage(val: 476);

        $results = $this->retrieveGalleries->retrieve(pagination: $pagination);

        self::assertTrue($results->hasEntries());

        self::assertSame(456, $results->totalResults());

        $items = $results->items();

        self::assertCount(2, $items);

        self::assertSame(
            [
                [
                    'keyImageUrl' => '/asset-url/gallery/entry/url/1',
                    'title' => 'Entry Title 1',
                    'url' => '/entry/url/1',
                ],
                [
                    'keyImageUrl' => '/asset-url/gallery/entry/url/1',
                    'title' => 'Entry Title 1',
                    'url' => '/entry/url/1',
                ],
            ],
            array_map(
                static function (GalleryItem $item): array {
                    return [
                        'keyImageUrl' => $item->keyImageUrl(),
                        'title' => $item->title(),
                        'url' => $item->url(),
                    ];
                },
                $results->items(),
            ),
        );

        self::assertCount(5, $this->calls);

        self::assertSame(
            [
                'object' => 'EntryQuery',
                'method' => 'section',
                'args' => ['galleries'],
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
                'object' => 'AssetsFieldHandler',
                'method' => 'getOne',
                'args' => [
                    $this->entries[0],
                    'gallery',
                ],
            ],
            $this->calls[3],
        );

        self::assertSame(
            [
                'object' => 'AssetsFieldHandler',
                'method' => 'getOne',
                'args' => [
                    $this->entries[1],
                    'gallery',
                ],
            ],
            $this->calls[4],
        );
    }
}
