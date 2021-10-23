<?php

declare(strict_types=1);

namespace App\Images\Resize\ResizeByWidth\ResizeOperations;

use App\Images\ImageCacheFileSystem;
use App\Images\Resize\ImageResizeFactory;
use Gumlet\ImageResize;
use Gumlet\ImageResizeException;
use League\Flysystem\Adapter\Local;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class ResizeLocalFileTest extends TestCase
{
    /** @var ImageResizeFactory&MockObject */
    private mixed $imageResizeFactoryStub;
    /** @var ImageCacheFileSystem&MockObject */
    private mixed $imageCacheFileSystemStub;

    /** @var mixed[] */
    private array $imageResizeCalls = [];

    /** @var mixed[] */
    private array $imageResizeFactoryCalls = [];

    /** @var mixed[] */
    private array $imageCacheFileSystemCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->imageResizeCalls          = [];
        $this->imageResizeFactoryCalls   = [];
        $this->imageCacheFileSystemCalls = [];

        $imageResizeStub = $this->createMock(
            ImageResize::class,
        );

        $imageResizeStub->method('resizeToWidth')
            ->willReturnCallback(function (int $width) use (
                $imageResizeStub
            ): ImageResize {
                $this->imageResizeCalls[] = [
                    'method' => 'resizeToWidth',
                    'width' => $width,
                ];

                return $imageResizeStub;
            });

        $imageResizeStub->method('save')
            ->willReturnCallback(function (string $filename) use (
                $imageResizeStub
            ): ImageResize {
                $this->imageResizeCalls[] = [
                    'method' => 'save',
                    'filename' => $filename,
                ];

                return $imageResizeStub;
            });

        $this->imageResizeFactoryStub = $this->createMock(
            ImageResizeFactory::class,
        );

        $this->imageResizeFactoryStub->method('make')
            ->willReturnCallback(function (string $filename) use (
                $imageResizeStub,
            ): ImageResize {
                $this->imageResizeFactoryCalls[] = [
                    'method' => 'make',
                    'filename' => $filename,
                ];

                return $imageResizeStub;
            });

        $imageCacheAdapter = $this->createMock(Local::class);

        $imageCacheAdapter->method('getPathPrefix')->willReturn(
            '/test/path/prefix/',
        );

        $this->imageCacheFileSystemStub = $this->createMock(
            ImageCacheFileSystem::class,
        );

        $this->imageCacheFileSystemStub->method('getAdapter')
            ->willReturn($imageCacheAdapter);

        $this->imageCacheFileSystemStub->method('createDir')
            ->willReturnCallback(function (string $dirname): bool {
                $this->imageCacheFileSystemCalls[] = [
                    'method' => 'createDir',
                    'dirname' => $dirname,
                ];

                return true;
            });
    }

    /**
     * @throws ImageResizeException
     */
    public function test(): void
    {
        $service = new ResizeLocalFile(
            sourceFileInfo: new SplFileInfo('/test/local/file.png'),
            imageResizeFactory: $this->imageResizeFactoryStub,
            imageCacheFileSystem: $this->imageCacheFileSystemStub,
        );

        $service->resize(
            targetFileName: 'test/target/filename.png',
            pixelWidth: 456,
        );

        self::assertCount(
            2,
            $this->imageResizeCalls,
        );

        self::assertSame(
            'resizeToWidth',
            $this->imageResizeCalls[0]['method'],
        );

        self::assertSame(
            456,
            $this->imageResizeCalls[0]['width'],
        );

        self::assertSame(
            'save',
            $this->imageResizeCalls[1]['method'],
        );

        self::assertSame(
            '/test/path/prefix/test/target/filename.png',
            $this->imageResizeCalls[1]['filename'],
        );

        self::assertCount(
            1,
            $this->imageResizeFactoryCalls,
        );

        self::assertSame(
            'make',
            $this->imageResizeFactoryCalls[0]['method'],
        );

        self::assertSame(
            '/test/local/file.png',
            $this->imageResizeFactoryCalls[0]['filename'],
        );

        self::assertCount(
            1,
            $this->imageCacheFileSystemCalls,
        );

        self::assertSame(
            'createDir',
            $this->imageCacheFileSystemCalls[0]['method'],
        );

        self::assertSame(
            'test/target',
            $this->imageCacheFileSystemCalls[0]['dirname'],
        );
    }
}
