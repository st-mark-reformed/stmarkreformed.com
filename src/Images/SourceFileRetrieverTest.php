<?php

declare(strict_types=1);

namespace App\Images;

use App\Shared\Files\PublicDirectoryFileSystem;
use App\Shared\Files\RemoteSplFileInfo;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use League\Flysystem\Adapter\Local;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use function assert;

class SourceFileRetrieverTest extends TestCase
{
    private SourceFileRetriever $service;

    private bool $fileSystemThrows = false;

    private bool $fileSystemHas = false;

    /** @var mixed[] */
    private array $fileSystemCalls = [];

    /** @var mixed[] */
    private array $guzzleCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileSystemCalls = [];
        $this->guzzleCalls     = [];

        $this->fileSystemThrows = false;

        $this->fileSystemHas = false;

        $guzzleBody = $this->createMock(
            StreamInterface::class,
        );

        $guzzleBody->method('getSize')->willReturn(823);

        $guzzleBody->method('__toString')->willReturn(
            'testBodyString',
        );

        $guzzleResponse = $this->createMock(
            ResponseInterface::class,
        );

        $guzzleResponse->method('getBody')->willReturn(
            $guzzleBody,
        );

        $guzzleClientStub = $this->createMock(
            GuzzleClient::class,
        );

        $guzzleClientStub->method('get')->willReturnCallback(
            function (string $uri) use (
                $guzzleResponse,
            ): ResponseInterface {
                $this->guzzleCalls[] = [
                    'method' => 'get',
                    'uri' => $uri,
                ];

                return $guzzleResponse;
            }
        );

        $fileSystemAdapter = $this->createMock(Local::class);

        $fileSystemAdapter->method('getPathPrefix')->willReturn(
            '/test/path/prefix/',
        );

        $fileSystemStub = $this->createMock(
            PublicDirectoryFileSystem::class,
        );

        $fileSystemStub->method('getAdapter')->willReturn(
            $fileSystemAdapter,
        );

        $fileSystemStub->method('has')->willReturnCallback(
            function (string $path): bool {
                if ($this->fileSystemThrows) {
                    throw new Exception();
                }

                $this->fileSystemCalls[] = [
                    'method' => 'has',
                    'path' => $path,
                ];

                return $this->fileSystemHas;
            }
        );

        $this->service = new SourceFileRetriever(
            guzzleClient: $guzzleClientStub,
            fileSystem: $fileSystemStub,
        );
    }

    public function testWhenFileSystemThrowsException(): void
    {
        $this->fileSystemThrows = true;

        $fileInfo = $this->service->retrieveInfo(
            pathOrUrl: '/test/path/to/file.png',
        );

        self::assertSame('', $fileInfo->getPathname());

        self::assertSame([], $this->fileSystemCalls);

        self::assertSame([], $this->guzzleCalls);
    }

    public function testLocal(): void
    {
        $this->fileSystemThrows = false;
        $this->fileSystemHas    = true;

        $fileInfo = $this->service->retrieveInfo(
            pathOrUrl: '/test/path/to/file.png',
        );

        $fileInfo2 = $this->service->retrieveInfo(
            pathOrUrl: '/test/path/to/file.png',
        );

        $isRemote = $fileInfo instanceof RemoteSplFileInfo;

        self::assertFalse($isRemote);

        self::assertSame(
            $fileInfo,
            $fileInfo2,
        );

        self::assertSame(
            '/test/path/prefix/test/path/to/file.png',
            $fileInfo->getPathname()
        );

        self::assertSame(
            [
                [
                    'method' => 'has',
                    'path' => '/test/path/to/file.png',
                ],
            ],
            $this->fileSystemCalls,
        );

        self::assertSame([], $this->guzzleCalls);
    }

    public function testRemote(): void
    {
        $this->fileSystemThrows = false;
        $this->fileSystemHas    = false;

        $fileInfo = $this->service->retrieveInfo(
            pathOrUrl: '/test/path/to/file.png',
        );

        $fileInfo2 = $this->service->retrieveInfo(
            pathOrUrl: '/test/path/to/file.png',
        );

        $isRemote = $fileInfo instanceof RemoteSplFileInfo;

        self::assertTrue($isRemote);

        assert($fileInfo instanceof RemoteSplFileInfo);

        self::assertSame(
            $fileInfo,
            $fileInfo2,
        );

        self::assertSame(
            '/test/path/to/file.png',
            $fileInfo->getPathname()
        );

        self::assertSame(
            823,
            $fileInfo->getSize(),
        );

        self::assertSame(
            'testBodyString',
            $fileInfo->getContent(),
        );

        self::assertSame(
            [
                [
                    'method' => 'has',
                    'path' => '/test/path/to/file.png',
                ],
            ],
            $this->fileSystemCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'get',
                    'uri' => '/test/path/to/file.png',
                ],
            ],
            $this->guzzleCalls,
        );
    }
}
