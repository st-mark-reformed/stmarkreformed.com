<?php

declare(strict_types=1);

namespace App\Images\Resize\ResizeByWidth\ResizeOperations;

use App\Images\ImageCacheFileSystem;
use App\Images\Resize\ImageResizeFactory;
use App\Shared\Files\RemoteSplFileInfo;
use App\Shared\Files\TempFilesFileSystem;
use Gumlet\ImageResize;
use Gumlet\ImageResizeException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ResizeRemoteFileTest extends TestCase
{
    /** @var ImageResizeFactory&MockObject */
    private mixed $imageResizeFactoryStub;
    /** @var TempFilesFileSystem&MockObject */
    private mixed $tempFilesFileSystemStub;
    /** @var ImageCacheFileSystem&MockObject */
    private mixed $imageCacheFileSystemStub;

    /** @var mixed[] */
    private array $imageResizeCalls = [];

    /** @var mixed[] */
    private array $imageResizeFactoryCalls = [];

    /** @var mixed[] */
    private array $tempFilesFileSystemCalls = [];

    /** @var mixed[] */
    private array $imageCacheFileSystemCalls = [];

    private bool $tempFilesFileSystemHasTarget = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->imageResizeCalls          = [];
        $this->imageResizeFactoryCalls   = [];
        $this->tempFilesFileSystemCalls  = [];
        $this->imageCacheFileSystemCalls = [];

        $this->tempFilesFileSystemHasTarget = false;

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

        $tempFilesAdapter = $this->createMock(Local::class);

        $tempFilesAdapter->method('getPathPrefix')->willReturn(
            '/cache/path/prefix/',
        );

        $this->tempFilesFileSystemStub = $this->createMock(
            TempFilesFileSystem::class,
        );

        $this->tempFilesFileSystemStub->method('getAdapter')
            ->willReturn($tempFilesAdapter);

        $this->tempFilesFileSystemStub->method('has')
            ->willReturnCallback(function (string $path): bool {
                $this->tempFilesFileSystemCalls[] = [
                    'method' => 'has',
                    'path' => $path,
                ];

                return $this->tempFilesFileSystemHasTarget;
            });

        $this->tempFilesFileSystemStub->method('delete')
            ->willReturnCallback(function (string $path): bool {
                $this->tempFilesFileSystemCalls[] = [
                    'method' => 'delete',
                    'path' => $path,
                ];

                return true;
            });

        $this->tempFilesFileSystemStub->method('write')
            ->willReturnCallback(function (
                string $path,
                string $contents,
            ): bool {
                $this->tempFilesFileSystemCalls[] = [
                    'method' => 'write',
                    'path' => $path,
                    'contents' => $contents,
                ];

                return true;
            });

        $this->tempFilesFileSystemStub->method('deleteDir')
            ->willReturnCallback(function (
                string $dirname,
            ): bool {
                $this->tempFilesFileSystemCalls[] = [
                    'method' => 'deleteDir',
                    'dirname' => $dirname,
                ];

                return true;
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
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function testWhenTempFilesHasTarget(): void
    {
        $this->tempFilesFileSystemHasTarget = true;

        $service = new ResizeRemoteFile(
            sourceFileInfo: new RemoteSplFileInfo(
                filename: '/test/source/file/name.jpg',
                size: 987,
                content: 'test-content',
            ),
            imageResizeFactory: $this->imageResizeFactoryStub,
            tempFilesFileSystem: $this->tempFilesFileSystemStub,
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
            '/cache/path/prefix/test/target/filename.png',
            $this->imageResizeFactoryCalls[0]['filename'],
        );

        self::assertCount(
            5,
            $this->tempFilesFileSystemCalls,
        );

        self::assertSame(
            'has',
            $this->tempFilesFileSystemCalls[0]['method'],
        );

        self::assertSame(
            'test/target/filename.png',
            $this->tempFilesFileSystemCalls[0]['path'],
        );

        self::assertSame(
            'delete',
            $this->tempFilesFileSystemCalls[1]['method'],
        );

        self::assertSame(
            'test/target/filename.png',
            $this->tempFilesFileSystemCalls[1]['path'],
        );

        self::assertSame(
            'write',
            $this->tempFilesFileSystemCalls[2]['method'],
        );

        self::assertSame(
            'test/target/filename.png',
            $this->tempFilesFileSystemCalls[2]['path'],
        );

        self::assertSame(
            'test-content',
            $this->tempFilesFileSystemCalls[2]['contents'],
        );

        self::assertSame(
            'delete',
            $this->tempFilesFileSystemCalls[3]['method'],
        );

        self::assertSame(
            'test/target/filename.png',
            $this->tempFilesFileSystemCalls[3]['path'],
        );

        self::assertSame(
            'deleteDir',
            $this->tempFilesFileSystemCalls[4]['method'],
        );

        self::assertSame(
            'test/target',
            $this->tempFilesFileSystemCalls[4]['dirname'],
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

    /**
     * @throws ImageResizeException
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function testWhenTempFilesDoesNotHaveTarget(): void
    {
        $this->tempFilesFileSystemHasTarget = false;

        $service = new ResizeRemoteFile(
            sourceFileInfo: new RemoteSplFileInfo(
                filename: '/test/source/file/name.jpg',
                size: 987,
                content: 'test-content',
            ),
            imageResizeFactory: $this->imageResizeFactoryStub,
            tempFilesFileSystem: $this->tempFilesFileSystemStub,
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
            '/cache/path/prefix/test/target/filename.png',
            $this->imageResizeFactoryCalls[0]['filename'],
        );

        self::assertCount(
            4,
            $this->tempFilesFileSystemCalls,
        );

        self::assertSame(
            'has',
            $this->tempFilesFileSystemCalls[0]['method'],
        );

        self::assertSame(
            'test/target/filename.png',
            $this->tempFilesFileSystemCalls[0]['path'],
        );

        self::assertSame(
            'write',
            $this->tempFilesFileSystemCalls[1]['method'],
        );

        self::assertSame(
            'test/target/filename.png',
            $this->tempFilesFileSystemCalls[1]['path'],
        );

        self::assertSame(
            'test-content',
            $this->tempFilesFileSystemCalls[1]['contents'],
        );

        self::assertSame(
            'delete',
            $this->tempFilesFileSystemCalls[2]['method'],
        );

        self::assertSame(
            'test/target/filename.png',
            $this->tempFilesFileSystemCalls[2]['path'],
        );

        self::assertSame(
            'deleteDir',
            $this->tempFilesFileSystemCalls[3]['method'],
        );

        self::assertSame(
            'test/target',
            $this->tempFilesFileSystemCalls[3]['dirname'],
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
