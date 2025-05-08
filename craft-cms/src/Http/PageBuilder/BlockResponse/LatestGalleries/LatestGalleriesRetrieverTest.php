<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\LatestGalleries;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\TestCase;
use yii\base\InvalidConfigException;

class LatestGalleriesRetrieverTest extends TestCase
{
    private LatestGalleriesRetriever $retriever;

    /** @var mixed[] */
    private array $entryQueryCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->entryQueryCalls = [];

        $assetStub1 = $this->createMock(Asset::class);

        $assetStub1->method('getUrl')->willReturn(
            'testAssetUrl1',
        );

        $assetQuery1Stub = $this->createMock(
            AssetQuery::class,
        );

        $assetQuery1Stub->method('one')->willReturn(
            $assetStub1,
        );

        $entrySpy1 = $this->createMock(Entry::class);

        $entrySpy1->expects(self::once())
            ->method('getFieldValue')
            ->with(self::equalTo('gallery'))
            ->willReturn($assetQuery1Stub);

        $entrySpy1->method('getUrl')->willReturn(
            'testEntryUrl1',
        );

        $entrySpy1->title = 'testEntryTitle1';

        $assetStub2 = $this->createMock(Asset::class);

        $assetStub2->method('getUrl')->willReturn(
            'testAsstUrl2',
        );

        $assetQuery2Stub = $this->createMock(
            AssetQuery::class,
        );

        $assetQuery2Stub->method('one')->willReturn(
            $assetStub2,
        );

        $entrySpy2 = $this->createMock(Entry::class);

        $entrySpy2->expects(self::once())
            ->method('getFieldValue')
            ->with(self::equalTo('gallery'))
            ->willReturn($assetQuery2Stub);

        $entrySpy2->method('getUrl')->willReturn(
            'testEntryUrl2',
        );

        $entrySpy2->title = 'testEntryTitle2';

        $entryQueryStub = $this->createMock(
            EntryQuery::class,
        );

        $entryQueryStub->method('section')->willReturnCallback(
            function (string $value) use (
                $entryQueryStub,
            ): EntryQuery {
                $this->entryQueryCalls[] = [
                    'method' => 'section',
                    'value' => $value,
                ];

                return $entryQueryStub;
            }
        );

        $entryQueryStub->method('orderBy')->willReturnCallback(
            function (string $columns) use (
                $entryQueryStub,
            ): EntryQuery {
                $this->entryQueryCalls[] = [
                    'method' => 'orderBy',
                    'columns' => $columns,
                ];

                return $entryQueryStub;
            }
        );

        $entryQueryStub->method('limit')->willReturnCallback(
            function (int $limit) use (
                $entryQueryStub,
            ): EntryQuery {
                $this->entryQueryCalls[] = [
                    'method' => 'limit',
                    'limit' => $limit,
                ];

                return $entryQueryStub;
            }
        );

        $entryQueryStub->method('all')->willReturnCallback(
            static function () use (
                $entrySpy1,
                $entrySpy2,
            ): array {
                return [
                    $entrySpy1,
                    $entrySpy2,
                ];
            }
        );

        $entryQueryFactoryStub = $this->createMock(
            EntryQueryFactory::class,
        );

        $entryQueryFactoryStub->method('make')->willReturn(
            $entryQueryStub,
        );

        $this->retriever = new LatestGalleriesRetriever(
            entryQueryFactory: $entryQueryFactoryStub,
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testRetrieve(): void
    {
        $galleryItems = $this->retriever->retrieve();

        self::assertCount(2, $galleryItems);

        $galleryItem1 = $galleryItems[0];

        self::assertSame(
            'testAssetUrl1',
            $galleryItem1->keyImageUrl(),
        );

        self::assertSame(
            'testEntryTitle1',
            $galleryItem1->title(),
        );

        self::assertSame(
            'testEntryUrl1',
            $galleryItem1->url(),
        );

        $galleryItem2 = $galleryItems[1];

        self::assertSame(
            'testAsstUrl2',
            $galleryItem2->keyImageUrl(),
        );

        self::assertSame(
            'testEntryTitle2',
            $galleryItem2->title(),
        );

        self::assertSame(
            'testEntryUrl2',
            $galleryItem2->url(),
        );

        self::assertCount(3, $this->entryQueryCalls);

        self::assertSame(
            [
                [
                    'method' => 'section',
                    'value' => 'galleries',
                ],
                [
                    'method' => 'orderBy',
                    'columns' => 'postDate desc',
                ],
                [
                    'method' => 'limit',
                    'limit' => 3,
                ],
            ],
            $this->entryQueryCalls,
        );
    }
}
