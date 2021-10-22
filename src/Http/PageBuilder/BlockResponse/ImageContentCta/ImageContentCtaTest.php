<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ImageContentCta;

use App\Http\Components\Link\Link;
use App\Http\Components\Link\LinkFactory;
use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Exception;
use percipioglobal\colourswatches\models\ColourSwatches;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use typedlinkfield\models\Link as LinkFieldModel;
use yii\base\InvalidConfigException;

use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class ImageContentCtaTest extends TestCase
{
    private Link $linkStub;
    /** @var MockObject&TwigEnvironment */
    private mixed $twigStub;
    /** @var LinkFactory&MockObject */
    private mixed $linkFactoryStub;
    /** @var MockObject&Markup */
    private mixed $contentTwigMarkupStub;
    /**
     * @var MockObject&LinkFieldModel
     * @phpstan-ignore-next-line
     */
    private mixed $ctaLinkFieldModelStub;
    /**
     * @var MatrixBlock&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $matrixBlockStub;

    /** @var mixed[] */
    private array $twigCalls = [];
    /** @var mixed[] */
    private array $linkFactoryCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->twigCalls        = [];
        $this->linkFactoryCalls = [];

        $this->linkStub = new Link(isEmpty: true);

        $this->contentTwigMarkupStub = $this->createMock(
            Markup::class,
        );

        $this->ctaLinkFieldModelStub = $this->createMock(
            LinkFieldModel::class,
        );

        $this->twigStub = $this->createMock(
            TwigEnvironment::class,
        );

        $this->twigStub->method('render')->willReturnCallback(
            function (string $name, array $context): string {
                $this->twigCalls[] = [
                    'method' => 'render',
                    'name' => $name,
                    'context' => $context,
                ];

                return 'fooBarTwigRender';
            }
        );

        $this->linkFactoryStub = $this->createMock(
            LinkFactory::class,
        );

        $this->linkFactoryStub->method('fromLinkFieldModel')
            ->willReturnCallback(
                function (LinkFieldModel $linkFieldModel): Link {
                    $this->linkFactoryCalls[] = [
                        'method' => 'fromLinkFieldModel',
                        'linkFieldModel' => $linkFieldModel,
                    ];

                    return $this->linkStub;
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
    }

    /**
     * @throws Exception
     */
    public function buildMatrixBlockFiledValueCallback(
        string $fieldHandle,
    ): mixed {
        return match ($fieldHandle) {
            'backgroundColor' => $this->mockBackgroundColor(),
            'image' => $this->mockImageQuery(),
            'contentField' => $this->contentTwigMarkupStub,
            'cta' => $this->ctaLinkFieldModelStub,
            'showTealOverlayOnImage' => true,
            'preHeadline' => 'testPreHeadline',
            'headline' => 'testHeadline',
            // @codeCoverageIgnoreStart
            default => throw new Exception(
                'FieldHandleNotImplemented',
            ),
            // @codeCoverageIgnoreEnd
        };
    }

    private function mockBackgroundColor(): ColourSwatches
    {
        $backgroundColorOption = new stdClass();

        $backgroundColorOption->tailwindColor = 'testTailwindColor';

        $backgroundColors = [$backgroundColorOption];

        $backgroundColorModel = $this->createMock(
            ColourSwatches::class
        );

        $backgroundColorModel->method('colors')->willReturn(
            $backgroundColors
        );

        return $backgroundColorModel;
    }

    /**
     * @phpstan-ignore-next-line
     */
    private function mockImageQuery(): AssetQuery
    {
        $image = $this->createMock(Asset::class);

        $image->method('getUrl')->willReturn('testImageUrl');

        $imageQuery = $this->createMock(AssetQuery::class);

        $imageQuery->method('one')->willReturn($image);

        return $imageQuery;
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
        $imageContentCta = new ImageContentCta(
            twig: $this->twigStub,
            linkFactory: $this->linkFactoryStub,
        );

        self::assertSame(
            'fooBarTwigRender',
            $imageContentCta->buildResponse(
                matrixBlock: $this->matrixBlockStub,
            ),
        );

        self::assertCount(1, $this->twigCalls);

        /** @psalm-suppress MixedArgument */
        self::assertCount(3, $this->twigCalls[0]);

        self::assertSame(
            'render',
            $this->twigCalls[0]['method']
        );

        self::assertSame(
            '@app/Http/PageBuilder/BlockResponse/ImageContentCta/ImageContentCta.twig',
            $this->twigCalls[0]['name']
        );

        /** @psalm-suppress MixedAssignment */
        $context = $this->twigCalls[0]['context'];

        /** @psalm-suppress MixedArgument */
        self::assertCount(1, $context);

        $contentModel = $context['contentModel'];

        assert($contentModel instanceof ImageContentCtaContentModel);

        self::assertSame(
            'testTailwindColor',
            $contentModel->tailwindBackgroundColor(),
        );

        self::assertSame(
            'testImageUrl',
            $contentModel->imageUrl(),
        );

        self::assertTrue($contentModel->showTealOverlayOnImage());

        self::assertSame(
            'testPreHeadline',
            $contentModel->preHeadline(),
        );

        self::assertSame(
            'testHeadline',
            $contentModel->headline(),
        );

        self::assertSame(
            $this->contentTwigMarkupStub,
            $contentModel->content(),
        );

        self::assertTrue($contentModel->cta()->isEmpty());

        self::assertCount(
            1,
            $this->linkFactoryCalls,
        );

        /** @psalm-suppress MixedArgument */
        self::assertCount(
            2,
            $this->linkFactoryCalls[0],
        );

        self::assertSame(
            'fromLinkFieldModel',
            $this->linkFactoryCalls[0]['method'],
        );

        self::assertSame(
            $this->ctaLinkFieldModelStub,
            $this->linkFactoryCalls[0]['linkFieldModel'],
        );
    }
}
