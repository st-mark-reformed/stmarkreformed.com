<?php

declare(strict_types=1);

namespace App\Images;

use App\Images\Queue\PushToQueueIfNotInQueue;
use craft\queue\BaseJob;
use PHPUnit\Framework\TestCase;

class ImageHandlerTest extends TestCase
{
    private ImageHandler $imageHandler;

    /** @var mixed[] */
    private array $fileNameCompilerCalls = [];

    /** @var mixed[] */
    private array $imageCacheFileSystemCalls = [];

    /** @var mixed[] */
    private array $pushToQueueIfNotInQueueCalls = [];

    private bool $fileSystemHas = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileNameCompilerCalls        = [];
        $this->imageCacheFileSystemCalls    = [];
        $this->pushToQueueIfNotInQueueCalls = [];

        $this->fileSystemHas = true;

        $fileNameCompilerStub = $this->createMock(
            FileNameCompiler::class,
        );

        $fileNameCompilerStub->method('forResizeToHeight')
            ->willReturnCallback(
                function (
                    string $pathOrUrl,
                    int $pixelHeight,
                ): string {
                    $this->fileNameCompilerCalls[] = [
                        'method' => 'forResizeToHeight',
                        'pathOrUrl' => $pathOrUrl,
                        'pixelHeight' => $pixelHeight,
                    ];

                    return 'test/file/name.jpg';
                }
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

        $imageCacheFileSystemStub = $this->createMock(
            ImageCacheFileSystem::class,
        );

        $imageCacheFileSystemStub->method('has')->willReturnCallback(
            function (string $path): bool {
                $this->imageCacheFileSystemCalls[] = [
                    'method' => 'has',
                    'path' => $path,
                ];

                return $this->fileSystemHas;
            }
        );

        $pushToQueueIfNotInQueueStub = $this->createMock(
            PushToQueueIfNotInQueue::class,
        );

        $pushToQueueIfNotInQueueStub->method('push')
            ->willReturnCallback(
                function (BaseJob $job): void {
                    $this->pushToQueueIfNotInQueueCalls[] = [
                        'method' => 'push',
                        'job' => $job,
                    ];
                }
            );

        $this->imageHandler = new ImageHandler(
            urlToImageCacheDirectory: '/test/url',
            fileNameCompiler: $fileNameCompilerStub,
            imageCacheFileSystem: $imageCacheFileSystemStub,
            pushToQueueIfNotInQueue: $pushToQueueIfNotInQueueStub,
        );
    }

    public function testResizeToWidthExists(): void
    {
        self::assertTrue($this->imageHandler->resizeToWidthExists(
            pathOrUrl: 'test/path/file.png',
            pixelWidth: 321,
        ));

        self::assertSame(
            [
                [
                    'method' => 'forResizeToWidth',
                    'pathOrUrl' => 'test/path/file.png',
                    'pixelWidth' => 321,
                ],
            ],
            $this->fileNameCompilerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'has',
                    'path' => 'test/file/name.jpg',
                ],
            ],
            $this->imageCacheFileSystemCalls,
        );

        self::assertSame(
            [],
            $this->pushToQueueIfNotInQueueCalls,
        );
    }

    public function testResizeToWidthWhenNotExistsAndNoReturnOriginal(): void
    {
        $this->fileSystemHas = false;

        self::assertNull($this->imageHandler->resizeToWidthByQueue(
            pathOrUrl: 'test/file/path.png',
            pixelWidth: 678,
        ));

        self::assertSame(
            [
                [
                    'method' => 'forResizeToWidth',
                    'pathOrUrl' => 'test/file/path.png',
                    'pixelWidth' => 678,
                ],
            ],
            $this->fileNameCompilerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'has',
                    'path' => 'test/file/name.jpg',
                ],
            ],
            $this->imageCacheFileSystemCalls,
        );

        self::assertCount(
            1,
            $this->pushToQueueIfNotInQueueCalls,
        );

        self::assertSame(
            'push',
            $this->pushToQueueIfNotInQueueCalls[0]['method'],
        );

        $pushedJob = $this->pushToQueueIfNotInQueueCalls[0]['job'];

        self::assertSame(
            'Resize image by width (678): test/file/path.png',
            $pushedJob->getDescription(),
        );
    }

    public function testResizeToWidthWhenNotExistsAndReturnOriginal(): void
    {
        $this->fileSystemHas = false;

        self::assertSame(
            'test/file/path.png',
            $this->imageHandler->resizeToWidthByQueue(
                pathOrUrl: 'test/file/path.png',
                pixelWidth: 678,
                returnOriginalIfNotExists: true,
            ),
        );

        self::assertSame(
            [
                [
                    'method' => 'forResizeToWidth',
                    'pathOrUrl' => 'test/file/path.png',
                    'pixelWidth' => 678,
                ],
            ],
            $this->fileNameCompilerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'has',
                    'path' => 'test/file/name.jpg',
                ],
            ],
            $this->imageCacheFileSystemCalls,
        );

        self::assertCount(
            1,
            $this->pushToQueueIfNotInQueueCalls,
        );

        self::assertSame(
            'push',
            $this->pushToQueueIfNotInQueueCalls[0]['method'],
        );

        $pushedJob = $this->pushToQueueIfNotInQueueCalls[0]['job'];

        self::assertSame(
            'Resize image by width (678): test/file/path.png',
            $pushedJob->getDescription(),
        );
    }

    public function testResizeToWidthWhenExists(): void
    {
        $this->fileSystemHas = true;

        self::assertSame(
            '/test/url/test/file/name.jpg',
            $this->imageHandler->resizeToWidthByQueue(
                pathOrUrl: 'test/file/path.png',
                pixelWidth: 678,
                returnOriginalIfNotExists: true,
            ),
        );

        self::assertSame(
            [
                [
                    'method' => 'forResizeToWidth',
                    'pathOrUrl' => 'test/file/path.png',
                    'pixelWidth' => 678,
                ],
            ],
            $this->fileNameCompilerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'has',
                    'path' => 'test/file/name.jpg',
                ],
            ],
            $this->imageCacheFileSystemCalls,
        );

        self::assertCount(
            0,
            $this->pushToQueueIfNotInQueueCalls,
        );
    }

    public function testResizeToHeightExists(): void
    {
        self::assertTrue($this->imageHandler->resizeToHeightExists(
            pathOrUrl: 'test/path/file.png',
            pixelHeight: 476,
        ));

        self::assertSame(
            [
                [
                    'method' => 'forResizeToHeight',
                    'pathOrUrl' => 'test/path/file.png',
                    'pixelHeight' => 476,
                ],
            ],
            $this->fileNameCompilerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'has',
                    'path' => 'test/file/name.jpg',
                ],
            ],
            $this->imageCacheFileSystemCalls,
        );

        self::assertSame(
            [],
            $this->pushToQueueIfNotInQueueCalls,
        );
    }

    public function testResizeToHeightWhenNotExistsAndNoReturnOriginal(): void
    {
        $this->fileSystemHas = false;

        self::assertNull($this->imageHandler->resizeToHeightByQueue(
            pathOrUrl: 'test/file/path.png',
            pixelHeight: 678,
        ));

        self::assertSame(
            [
                [
                    'method' => 'forResizeToHeight',
                    'pathOrUrl' => 'test/file/path.png',
                    'pixelHeight' => 678,
                ],
            ],
            $this->fileNameCompilerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'has',
                    'path' => 'test/file/name.jpg',
                ],
            ],
            $this->imageCacheFileSystemCalls,
        );

        self::assertCount(
            1,
            $this->pushToQueueIfNotInQueueCalls,
        );

        self::assertSame(
            'push',
            $this->pushToQueueIfNotInQueueCalls[0]['method'],
        );

        $pushedJob = $this->pushToQueueIfNotInQueueCalls[0]['job'];

        self::assertSame(
            'Resize image by height (678): test/file/path.png',
            $pushedJob->getDescription(),
        );
    }

    public function testResizeToHeightWhenNotExistsAndReturnOriginal(): void
    {
        $this->fileSystemHas = false;

        self::assertSame(
            'test/file/path.png',
            $this->imageHandler->resizeToHeightByQueue(
                pathOrUrl: 'test/file/path.png',
                pixelHeight: 678,
                returnOriginalIfNotExists: true,
            ),
        );

        self::assertSame(
            [
                [
                    'method' => 'forResizeToHeight',
                    'pathOrUrl' => 'test/file/path.png',
                    'pixelHeight' => 678,
                ],
            ],
            $this->fileNameCompilerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'has',
                    'path' => 'test/file/name.jpg',
                ],
            ],
            $this->imageCacheFileSystemCalls,
        );

        self::assertCount(
            1,
            $this->pushToQueueIfNotInQueueCalls,
        );

        self::assertSame(
            'push',
            $this->pushToQueueIfNotInQueueCalls[0]['method'],
        );

        $pushedJob = $this->pushToQueueIfNotInQueueCalls[0]['job'];

        self::assertSame(
            'Resize image by height (678): test/file/path.png',
            $pushedJob->getDescription(),
        );
    }

    public function testResizeToHeightWhenExists(): void
    {
        $this->fileSystemHas = true;

        self::assertSame(
            '/test/url/test/file/name.jpg',
            $this->imageHandler->resizeToHeightByQueue(
                pathOrUrl: 'test/file/path.png',
                pixelHeight: 678,
                returnOriginalIfNotExists: true,
            ),
        );

        self::assertSame(
            [
                [
                    'method' => 'forResizeToHeight',
                    'pathOrUrl' => 'test/file/path.png',
                    'pixelHeight' => 678,
                ],
            ],
            $this->fileNameCompilerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'has',
                    'path' => 'test/file/name.jpg',
                ],
            ],
            $this->imageCacheFileSystemCalls,
        );

        self::assertCount(
            0,
            $this->pushToQueueIfNotInQueueCalls,
        );
    }
}
