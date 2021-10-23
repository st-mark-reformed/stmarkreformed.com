<?php

declare(strict_types=1);

namespace App\Images\Resize\ResizeByWidth\ResizeOperations;

use App\Images\ImageCacheFileSystem;
use App\Images\Resize\ImageResizeFactory;
use App\Shared\Files\RemoteSplFileInfo;
use App\Shared\Files\TempFilesFileSystem;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class ResizeOperationFactoryTest extends TestCase
{
    private ResizeOperationFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ResizeOperationFactory(
            imageResizeFactory: $this->createMock(
                ImageResizeFactory::class,
            ),
            tempFilesFileSystem: $this->createMock(
                TempFilesFileSystem::class,
            ),
            imageCacheFileSystem: $this->createMock(
                ImageCacheFileSystem::class,
            ),
        );
    }

    public function testWhenInfoIsRemote(): void
    {
        self::assertInstanceOf(
            ResizeRemoteFile::class,
            $this->factory->make(
                sourceFileInfo: new RemoteSplFileInfo(
                    filename: 'test-filename.png',
                    size: 534,
                    content: 'test-content',
                ),
            ),
        );
    }

    public function testWhenInfoIsLocal(): void
    {
        self::assertInstanceOf(
            ResizeLocalFile::class,
            $this->factory->make(
                sourceFileInfo: new SplFileInfo(
                    'test-filename.png',
                ),
            ),
        );
    }
}
