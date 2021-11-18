<?php

declare(strict_types=1);

namespace App\Images\Resize\ResizeByWidth;

use App\Images\FileNameCompiler;
use App\Images\ImageCacheFileSystem;
use App\Images\Resize\ResizeByWidth\ResizeOperations\ResizeOperationContract;
use App\Images\Resize\ResizeByWidth\ResizeOperations\ResizeOperationFactory;
use App\Images\SourceFileRetriever;
use BuzzingPixel\StaticCache\CacheApi\CacheApiContract;
use League\Flysystem\Adapter\Local;
use PHPUnit\Framework\TestCase;
use Spatie\ImageOptimizer\OptimizerChain;
use SplFileInfo;

class ResizeByWidthTest extends TestCase
{
    private SplFileInfo $sourceFileInfoStub;

    private ResizeByWidth $resizeByWidth;

    /** @var mixed[] */
    private array $optimizerChainCalls = [];

    /** @var mixed[] */
    private array $staticCacheApiCalls = [];

    /** @var mixed[] */
    private array $fileNameCompilerCalls = [];

    /** @var mixed[] */
    private array $sourceFileRetrieverCalls = [];

    /** @var mixed[] */
    private array $resizeOperationCalls = [];

    /** @var mixed[] */
    private array $resizeOperationFactoryCalls = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->optimizerChainCalls         = [];
        $this->staticCacheApiCalls         = [];
        $this->fileNameCompilerCalls       = [];
        $this->sourceFileRetrieverCalls    = [];
        $this->resizeOperationCalls        = [];
        $this->resizeOperationFactoryCalls = [];

        $this->sourceFileInfoStub = new SplFileInfo('testFileInfo');

        $optimizerChainStub = $this->createMock(
            OptimizerChain::class,
        );

        $optimizerChainStub->method('optimize')
            ->willReturnCallback(
                function (string $pathToImage): void {
                    $this->optimizerChainCalls[] = [
                        'method' => 'optimize',
                        'pathToImage' => $pathToImage,
                    ];
                }
            );

        $staticCacheApiStub = $this->createMock(
            CacheApiContract::class,
        );

        $staticCacheApiStub->method('clearAllCache')
            ->willReturnCallback(
                function (): void {
                    $this->staticCacheApiCalls[] = ['method' => 'clearAllCache'];
                }
            );

        $fileNameCompilerStub = $this->createMock(
            FileNameCompiler::class,
        );

        $fileNameCompilerStub->method('forResizeToWidth')
            ->willReturnCallback(
                function (
                    string $pathOrUrl,
                    int $pixelWidth,
                ): string {
                    $this->fileNameCompilerCalls[] = [
                        'method' => 'forResizeToWidth',
                        'pathOrUrl' => $pathOrUrl,
                        'pixelWidth' => $pixelWidth,
                    ];

                    return 'test/file/name.jpg';
                }
            );

        $sourceFileRetrieverStub = $this->createMock(
            SourceFileRetriever::class,
        );

        $sourceFileRetrieverStub->method('retrieveInfo')
            ->willReturnCallback(
                function (string $pathOrUrl): SplFileInfo {
                    $this->sourceFileRetrieverCalls[] = [
                        'method' => 'retrieveInfo',
                        'pathOrUrl' => $pathOrUrl,
                    ];

                    return $this->sourceFileInfoStub;
                }
            );

        $imageCacheAdapter = $this->createMock(Local::class);

        $imageCacheAdapter->method('getPathPrefix')->willReturn(
            '/test/path/prefix/',
        );

        $imageCacheFileSystemStub = $this->createMock(
            ImageCacheFileSystem::class,
        );

        $imageCacheFileSystemStub->method('getAdapter')
            ->willReturn($imageCacheAdapter);

        $resizeOperationStub = $this->createMock(
            ResizeOperationContract::class,
        );

        $resizeOperationStub->method('resize')->willReturnCallback(
            function (string $targetFileName, int $pixelWidth): void {
                $this->resizeOperationCalls[] = [
                    'method' => 'resize',
                    'targetFileName' => $targetFileName,
                    'pixelWidth' => $pixelWidth,
                ];
            }
        );

        $resizeOperationFactoryStub = $this->createMock(
            ResizeOperationFactory::class,
        );

        $resizeOperationFactoryStub->method('make')
            ->willReturnCallback(
                function (SplFileInfo $sourceFileInfo) use (
                    $resizeOperationStub,
                ): ResizeOperationContract {
                    $this->resizeOperationFactoryCalls[] = [
                        'method' => 'make',
                        'sourceFileInfo' => $sourceFileInfo,
                    ];

                    return $resizeOperationStub;
                }
            );

        $this->resizeByWidth = new ResizeByWidth(
            optimizerChain: $optimizerChainStub,
            staticCacheApi: $staticCacheApiStub,
            fileNameCompiler: $fileNameCompilerStub,
            sourceFileRetriever: $sourceFileRetrieverStub,
            imageCacheFileSystem: $imageCacheFileSystemStub,
            resizeOperationFactory: $resizeOperationFactoryStub,
        );
    }

    public function testResize(): void
    {
        $this->resizeByWidth->resize(
            pathOrUrl: 'test/path/url.png',
            pixelWidth: 876,
        );

        self::assertCount(
            1,
            $this->optimizerChainCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'optimize',
                    'pathToImage' => '/test/path/prefix/test/file/name.jpg',
                ],
            ],
            $this->optimizerChainCalls,
        );

        self::assertSame(
            [
                ['method' => 'clearAllCache'],
            ],
            $this->staticCacheApiCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'forResizeToWidth',
                    'pathOrUrl' => 'test/path/url.png',
                    'pixelWidth' => 876,
                ],
            ],
            $this->fileNameCompilerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'retrieveInfo',
                    'pathOrUrl' => 'test/path/url.png',
                ],
            ],
            $this->sourceFileRetrieverCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'resize',
                    'targetFileName' => 'test/file/name.jpg',
                    'pixelWidth' => 876,
                ],
            ],
            $this->resizeOperationCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'make',
                    'sourceFileInfo' => $this->sourceFileInfoStub,
                ],
            ],
            $this->resizeOperationFactoryCalls,
        );
    }
}
