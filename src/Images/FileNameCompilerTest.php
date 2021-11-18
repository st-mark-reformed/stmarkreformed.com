<?php

declare(strict_types=1);

namespace App\Images;

use PHPUnit\Framework\TestCase;
use SplFileInfo;

class FileNameCompilerTest extends TestCase
{
    private FileNameCompiler $fileNameCompiler;

    /** @var mixed[] */
    private array $sourceFileRetrieverCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $fileInfoStub = $this->createMock(
            SplFileInfo::class,
        );

        $fileInfoStub->method('getSize')->willReturn(734);

        $fileInfoStub->method('getBasename')->willReturn(
            'test-base-name.gif',
        );

        $sourceFileRetrieverStub = $this->createMock(
            SourceFileRetriever::class,
        );

        $sourceFileRetrieverStub->method('retrieveInfo')
            ->willReturnCallback(function (string $pathOrUrl) use (
                $fileInfoStub,
            ): SplFileInfo {
                $this->sourceFileRetrieverCalls[] = [
                    'method' => 'retrieveInfo',
                    'pathOrUrl' => $pathOrUrl,
                ];

                return $fileInfoStub;
            });

        $this->fileNameCompiler = new FileNameCompiler(
            sourceFileRetriever: $sourceFileRetrieverStub,
        );
    }

    public function testForResizeToWidth(): void
    {
        $fileName = $this->fileNameCompiler->forResizeToWidth(
            pathOrUrl: '/test/path/url/file.jpg',
            pixelWidth: 980,
        );

        self::assertSame(
            'resize-to-width/e8fac89edeab35bc537fb68cb6d38b57/test-base-name.gif',
            $fileName,
        );

        self::assertSame(
            [
                [
                    'method' => 'retrieveInfo',
                    'pathOrUrl' => '/test/path/url/file.jpg',
                ],
            ],
            $this->sourceFileRetrieverCalls,
        );
    }

    public function testForResizeToHeight(): void
    {
        $fileName = $this->fileNameCompiler->forResizeToHeight(
            pathOrUrl: '/test/path/url/file.jpg',
            pixelHeight: 980,
        );

        self::assertSame(
            'resize-to-height/7c6edaf52b1ce61cf74ece8c0d70801f/test-base-name.gif',
            $fileName,
        );

        self::assertSame(
            [
                [
                    'method' => 'retrieveInfo',
                    'pathOrUrl' => '/test/path/url/file.jpg',
                ],
            ],
            $this->sourceFileRetrieverCalls,
        );
    }
}
