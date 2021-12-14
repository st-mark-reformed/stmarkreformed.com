<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\DownloadAudio;

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
    private RetrieveResult $retrieveResult;

    private Entry $entry;

    private bool $entryQueryReturnsEntry = false;

    private bool $assetHandlerReturnsAsset = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entry = $this->createMock(Entry::class);

        $this->entryQueryReturnsEntry = false;

        $this->assetHandlerReturnsAsset = false;

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

        $entryQuery->method('one')->willReturnCallback(
            function (): ?Entry {
                return $this->entryQueryReturnsEntry ? $this->entry : null;
            }
        );

        return $entryQuery;
    }

    /**
     * @return AssetsFieldHandler&MockObject
     */
    private function mockAssetsFieldHandler(): AssetsFieldHandler|MockObject
    {
        $handler = $this->createMock(
            AssetsFieldHandler::class,
        );

        $handler->method('getOneOrNull')->willReturnCallback(
            /** @phpstan-ignore-next-line */
            function (): ?Asset {
                $asset = null;

                if ($this->assetHandlerReturnsAsset) {
                    $asset = $this->createMock(Asset::class);

                    $asset->method('getVolume')->willReturn(
                        $this->mockVolume(),
                    );

                    $asset->method('getMimeType')->willReturn(
                        'asset-mime-type',
                    );

                    $asset->method('getPath')->willReturn(
                        'asset-path',
                    );
                }

                return $this->genericCall(
                    object: 'AssetsFieldHandler',
                    return: $asset,
                );
            }
        );

        return $handler;
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
    public function testFromSlugWhenSlugIsEmpty(): void
    {
        $result = $this->retrieveResult->fromSlug(slug: '');

        self::assertFalse($result->hasResult());

        self::assertSame([], $this->calls);
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testFromSlugWhenNoEntryResult(): void
    {
        $result = $this->retrieveResult->fromSlug(slug: 'fooBarSlug');

        self::assertFalse($result->hasResult());

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['internalMessages'],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testFromSlugWhenNoAsset(): void
    {
        $this->entryQueryReturnsEntry = true;

        $result = $this->retrieveResult->fromSlug(slug: 'fooBarSlug');

        self::assertFalse($result->hasResult());

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['internalMessages'],
                ],
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getOneOrNull',
                    'args' => [
                        $this->entry,
                        'internalAudio',
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testFromSlug(): void
    {
        $this->entryQueryReturnsEntry = true;

        $this->assetHandlerReturnsAsset = true;

        $result = $this->retrieveResult->fromSlug(slug: 'fooBarSlug');

        self::assertTrue($result->hasResult());

        self::assertSame(
            'asset-mime-type',
            $result->mimeType(),
        );

        self::assertSame(
            'root-path/asset-path',
            $result->pathOnServer(),
        );

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['internalMessages'],
                ],
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getOneOrNull',
                    'args' => [
                        $this->entry,
                        'internalAudio',
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
