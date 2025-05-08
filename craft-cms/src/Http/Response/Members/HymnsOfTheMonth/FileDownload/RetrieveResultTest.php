<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\FileDownload;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\Testing\TestCase;
use craft\elements\Asset;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use craft\volumes\Local;
use PHPUnit\Framework\MockObject\MockObject;
use yii\base\InvalidConfigException;

class RetrieveResultTest extends TestCase
{
    private Entry $entry;

    private bool $entryQueryReturnsEntry = false;

    private RetrieveResult $retrieveResult;

    /** @var Asset[] */
    private array $asset1 = [];

    /** @var Asset[] */
    private array $asset2 = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->entry = $this->createMock(Entry::class);

        $this->entryQueryReturnsEntry = false;

        $this->asset1 = [];

        $this->asset2 = [];

        $this->retrieveResult = new RetrieveResult(
            entryQueryFactory: $this->mockEntryQueryFactory(),
            assetsFieldHandler: $this->mockAssetsFieldHandler(),
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

        $entryQuery->method('slug')->willReturnCallback(
            $methodCallback
        );

        $entryQuery->method('one')->willReturnCallback(
            function (): ?Entry {
                return $this->entryQueryReturnsEntry ? $this->entry : null;
            }
        );

        return $entryQuery;
    }

    private int $assetGetAllCallNum = 0;

    private function mockAssetsFieldHandler(): AssetsFieldHandler
    {
        $mock = $this->createMock(AssetsFieldHandler::class);

        $mock->method('getAll')->willReturnCallback(
            function (): array {
                $this->assetGetAllCallNum += 1;

                if ($this->assetGetAllCallNum === 1) {
                    $assets = $this->asset1;
                } else {
                    $assets = $this->asset2;
                }

                return $this->genericCall(
                    object: 'AssetsFieldHandler',
                    return: $assets,
                );
            }
        );

        return $mock;
    }

    /**
     * @return Local&MockObject
     *
     * @phpstan-ignore-next-line
     */
    private function mockVolume(): Local|MockObject
    {
        $mock = $this->createMock(Local::class);

        $mock->method('getRootPath')->willReturn(
            'root-path',
        );

        return $mock;
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testFromAttributesWhenSlugAndFilePathIsEmpty(): void
    {
        $result = $this->retrieveResult->fromAttributes(
            slug: '',
            filePath: '',
        );

        self::assertFalse($result->hasResult());

        self::assertSame([], $this->calls);
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testFromAttributesWhenSlugIsEmpty(): void
    {
        $result = $this->retrieveResult->fromAttributes(
            slug: '',
            filePath: 'foo/bar/filepath',
        );

        self::assertFalse($result->hasResult());

        self::assertSame([], $this->calls);
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testFromAttributesWhenFilePathIsEmpty(): void
    {
        $result = $this->retrieveResult->fromAttributes(
            slug: 'fooBarSlug',
            filePath: '',
        );

        self::assertFalse($result->hasResult());

        self::assertSame([], $this->calls);
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testFromAttributesWhenNoEntryResult(): void
    {
        $result = $this->retrieveResult->fromAttributes(
            slug: 'fooBarSlug',
            filePath: 'foo/bar/filepath',
        );

        self::assertFalse($result->hasResult());

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['hymnsOfTheMonth'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'slug',
                    'args' => ['fooBarSlug'],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws InvalidFieldException
     */
    public function testFromAttributesWhenNoAssets(): void
    {
        $this->entryQueryReturnsEntry = true;

        $result = $this->retrieveResult->fromAttributes(
            slug: 'fooBarSlug',
            filePath: 'foo/bar/filepath',
        );

        self::assertFalse($result->hasResult());

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['hymnsOfTheMonth'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'slug',
                    'args' => ['fooBarSlug'],
                ],
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getAll',
                    'args' => [
                        $this->entry,
                        'hymnOfTheMonthMusic',
                    ],
                ],
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getAll',
                    'args' => [
                        $this->entry,
                        'hymnOfTheMonthPracticeTracks',
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws InvalidFieldException
     */
    public function testFromAttributesWhenNoMatchingAssets(): void
    {
        $this->entryQueryReturnsEntry = true;

        $this->asset1 = [$this->createMock(Asset::class)];

        $this->asset2 = [$this->createMock(Asset::class)];

        $result = $this->retrieveResult->fromAttributes(
            slug: 'fooBarSlug',
            filePath: 'foo/bar/filepath',
        );

        self::assertFalse($result->hasResult());

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['hymnsOfTheMonth'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'slug',
                    'args' => ['fooBarSlug'],
                ],
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getAll',
                    'args' => [
                        $this->entry,
                        'hymnOfTheMonthMusic',
                    ],
                ],
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getAll',
                    'args' => [
                        $this->entry,
                        'hymnOfTheMonthPracticeTracks',
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws InvalidFieldException
     */
    public function testFromAttributes(): void
    {
        $this->entryQueryReturnsEntry = true;

        $this->asset1 = [$this->createMock(Asset::class)];

        $asset2 = $this->createMock(Asset::class);
        $asset2->method('getPath')->willReturn(
            'foo/bar/filepath'
        );
        $asset2->method('getVolume')->willReturn(
            $this->mockVolume(),
        );
        $asset2->method('getMimeType')->willReturn(
            'fooMimeType',
        );

        $asset3 = $this->createMock(Asset::class);
        $asset3->method('getPath')->willReturn(
            'foo/bar/filepath'
        );

        $this->asset2 = [$asset2, $asset3];

        $result = $this->retrieveResult->fromAttributes(
            slug: 'fooBarSlug',
            filePath: 'foo/bar/filepath',
        );

        self::assertTrue($result->hasResult());

        self::assertSame('fooMimeType', $result->mimeType());

        self::assertSame(
            'root-path/foo/bar/filepath',
            $result->pathOnServer(),
        );

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['hymnsOfTheMonth'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'slug',
                    'args' => ['fooBarSlug'],
                ],
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getAll',
                    'args' => [
                        $this->entry,
                        'hymnOfTheMonthMusic',
                    ],
                ],
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getAll',
                    'args' => [
                        $this->entry,
                        'hymnOfTheMonthPracticeTracks',
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
