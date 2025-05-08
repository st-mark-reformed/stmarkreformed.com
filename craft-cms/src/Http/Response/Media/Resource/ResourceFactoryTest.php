<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resource;

use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use App\Shared\Testing\TestCase;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Markup;
use yii\base\InvalidConfigException;

class ResourceFactoryTest extends TestCase
{
    private ResourceFactory $factory;

    private Entry $entry;

    /** @phpstan-ignore-next-line */
    private MatrixBlock $matrixBlock1;

    /** @phpstan-ignore-next-line */
    private MatrixBlock $matrixBlock2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entry = $this->createMock(Entry::class);

        $this->entry->title = 'Test Entry Title';

        $this->matrixBlock1 = $this->createMock(
            MatrixBlock::class,
        );

        $this->matrixBlock2 = $this->createMock(
            MatrixBlock::class,
        );

        $this->factory = new ResourceFactory(
            genericHandler: $this->mockGenericHandler(),
            assetsFieldHandler: $this->mockAssetsFieldHandler(),
            matrixFieldHandler: $this->mockMatrixFieldHandler(),
        );
    }

    /**
     * @return GenericHandler&MockObject
     */
    private function mockGenericHandler(): GenericHandler|MockObject
    {
        $handler = $this->createMock(GenericHandler::class);

        $handler->method('getTwigMarkup')->willReturnCallback(
            function (): Markup {
                return $this->genericCall(
                    object: 'GenericHandler',
                    return: new Markup(
                        'getTwigMarkup',
                        'UTF-8'
                    ),
                );
            }
        );

        return $handler;
    }

    /**
     * @return AssetsFieldHandler&MockObject
     */
    private function mockAssetsFieldHandler(): AssetsFieldHandler|MockObject
    {
        $handler = $this->createMock(
            AssetsFieldHandler::class,
        );

        $handler->method('getOne')->willReturnCallback(
            function (): Asset {
                $asset = $this->createMock(Asset::class);

                $asset->method('getUrl')->willReturn(
                    '/test/asset/url',
                );

                $asset->method('getFilename')->willReturn(
                    'test-asset-filename.pdf',
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
     * @return MatrixFieldHandler&MockObject
     */
    private function mockMatrixFieldHandler(): MatrixFieldHandler|MockObject
    {
        $handler = $this->createMock(
            MatrixFieldHandler::class
        );

        $handler->method('getAll')->willReturnCallback(
            function (): array {
                return $this->genericCall(
                    object: 'MatrixFieldHandler',
                    return: [
                        $this->matrixBlock1,
                        $this->matrixBlock2,
                    ],
                );
            }
        );

        return $handler;
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testMakeFromEntry(): void
    {
        $resourceItem = $this->factory->makeFromEntry(entry: $this->entry);

        self::assertSame(
            'Test Entry Title',
            $resourceItem->title(),
        );

        self::assertTrue($resourceItem->hasBody());

        self::assertSame(
            'getTwigMarkup',
            (string) $resourceItem->body(),
        );

        self::assertSame(
            [
                [
                    'url' => '/test/asset/url',
                    'filename' => 'test-asset-filename.pdf',
                ],
                [
                    'url' => '/test/asset/url',
                    'filename' => 'test-asset-filename.pdf',
                ],
            ],
            $resourceItem->map(
                static fn (ResourceDownloadItem $item) => [
                    'url' => $item->url(),
                    'filename' => $item->filename(),
                ]
            ),
        );

        self::assertSame(
            [
                [
                    'object' => 'MatrixFieldHandler',
                    'method' => 'getAll',
                    'args' => [
                        $this->entry,
                        'resourceDownloads',
                    ],
                ],
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getOne',
                    'args' => [
                        $this->matrixBlock1,
                        'file',
                    ],
                ],
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getOne',
                    'args' => [
                        $this->matrixBlock2,
                        'file',
                    ],
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getTwigMarkup',
                    'args' => [
                        $this->entry,
                        'body',
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
