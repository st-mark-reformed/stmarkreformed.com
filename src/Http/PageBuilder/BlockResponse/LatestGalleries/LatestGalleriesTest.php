<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\LatestGalleries;

use App\Http\PageBuilder\BlockResponse\LatestGalleries\Entities\GalleryItem;
use App\Http\PageBuilder\BlockResponse\LatestGalleries\Entities\LatestGalleriesContentModel;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedAssignment
 */
class LatestGalleriesTest extends TestCase
{
    private LatestGalleries $service;

    /**
     * @var MatrixBlock&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $matrixBlockStub;

    /** @var GalleryItem[] */
    private array $galleryItemsStubs;

    /** @var mixed[] */
    private array $twigCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->twigCalls = [];

        $twigStub = $this->createMock(
            TwigEnvironment::class,
        );

        $twigStub->method('render')->willReturnCallback(
            function (string $name, array $context): string {
                $this->twigCalls[] = [
                    'method' => 'render',
                    'name' => $name,
                    'context' => $context,
                ];

                return 'fooBarTwigRender';
            }
        );

        $this->matrixBlockStub = $this->createMock(
            MatrixBlock::class,
        );

        $this->matrixBlockStub->method('getFieldValue')
            ->willReturnCallback([
                $this,
                'buildMatrixBlockFiledValueCallback',
            ]);

        $this->galleryItemsStubs = [
            new GalleryItem(
                keyImageUrl: 'testImageUrl1',
                title: 'testTitle1',
                url: 'testUrl1',
            ),
            new GalleryItem(
                keyImageUrl: 'testImageUrl2',
                title: 'testTitle2',
                url: 'testUrl2',
            ),
        ];

        $latestGalleriesRetrieverStub = $this->createMock(
            LatestGalleriesRetriever::class,
        );

        $latestGalleriesRetrieverStub->method('retrieve')
            ->willReturn($this->galleryItemsStubs);

        $this->service = new LatestGalleries(
            twig: $twigStub,
            latestGalleriesRetriever: $latestGalleriesRetrieverStub,
        );
    }

    /**
     * @throws Exception
     */
    public function buildMatrixBlockFiledValueCallback(
        string $fieldHandle,
    ): string {
        return match ($fieldHandle) {
            'heading' => 'testHeading',
            'subHeading' => 'testSubHeading',
            // @codeCoverageIgnoreStart
            default => throw new Exception(
                'FieldHandleNotImplemented',
            ),
            // @codeCoverageIgnoreEnd
        };
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testBuildResponse(): void
    {
        self::assertSame(
            'fooBarTwigRender',
            $this->service->buildResponse(
                matrixBlock: $this->matrixBlockStub,
            ),
        );

        self::assertCount(
            1,
            $this->twigCalls,
        );

        self::assertSame(
            'render',
            $this->twigCalls[0]['method'],
        );

        self::assertSame(
            '@app/Http/PageBuilder/BlockResponse/LatestGalleries/LatestGalleries.twig',
            $this->twigCalls[0]['name'],
        );

        $context = $this->twigCalls[0]['context'];

        self::assertCount(1, $context);

        $contentModel = $context['contentModel'];

        assert($contentModel instanceof LatestGalleriesContentModel);

        self::assertTrue($contentModel->hasHeadings());

        self::assertSame(
            'testHeading',
            $contentModel->heading(),
        );

        self::assertSame(
            'testSubHeading',
            $contentModel->subHeading(),
        );

        self::assertSame(
            $this->galleryItemsStubs,
            $contentModel->galleryItems(),
        );
    }
}
