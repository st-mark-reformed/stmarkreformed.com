<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ImageContentCta;

use App\Http\Components\Link\Link;
use App\Http\Components\Link\LinkFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\ColourSwatches\ColorOptionFromElementField;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\LinkField\LinkFieldHandler;
use craft\base\Element;
use craft\elements\Asset;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use typedlinkfield\models\Link as LinkFieldModel;
use yii\base\InvalidConfigException;

use function assert;

class ImageContentCtaTest extends TestCase
{
    private ImageContentCta $service;

    /**
     * @var MatrixBlock&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $matrixBlock;

    /** @var mixed[] */
    private array $twigCalls = [];

    /** @var mixed[] */
    private array $linkFactoryCalls = [];

    /** @var mixed[] */
    private array $genericHandlerCalls = [];

    /** @var mixed[] */
    private array $linkFieldHandlerCalls = [];

    /** @var mixed[] */
    private array $assetsFieldHandlerCalls = [];

    /** @var mixed[] */
    private array $colorOptionFromElementFieldCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockMatrixBlock();

        $this->service = new ImageContentCta(
            twig: $this->mockTwig(),
            linkFactory: $this->mockLinkFactory(),
            genericHandler: $this->mockGenericHandler(),
            linkFieldHandler: $this->mockLinkFieldHandler(),
            assetsFieldHandler: $this->mockAssetsFieldHandler(),
            colorOptionFromElementField: $this->mockColorOptionFromElementField(),
        );
    }

    private function mockMatrixBlock(): void
    {
        $this->matrixBlock = $this->createMock(
            MatrixBlock::class,
        );
    }

    /**
     * @return MockObject&TwigEnvironment
     */
    private function mockTwig(): mixed
    {
        $this->twigCalls = [];

        $twig = $this->createMock(
            TwigEnvironment::class,
        );

        $twig->method('render')->willReturnCallback(
            function (string $name, array $context): string {
                $this->twigCalls[] = [
                    'method' => 'render',
                    'name' => $name,
                    'context' => $context,
                ];

                return 'twigRenderReturn';
            }
        );

        return $twig;
    }

    /**
     * @return MockObject&LinkFactory
     */
    private function mockLinkFactory(): mixed
    {
        $this->linkFactoryCalls = [];

        $linkFactory = $this->createMock(
            LinkFactory::class,
        );

        $linkFactory->method('fromLinkFieldModel')
            ->willReturnCallback(
                function (LinkFieldModel $linkFieldModel): Link {
                    $this->linkFactoryCalls[] = [
                        'method' => 'render',
                        'linkFieldModel' => $linkFieldModel,
                    ];

                    return new Link(
                        isEmpty: false,
                        content: 'testContent',
                    );
                }
            );

        return $linkFactory;
    }

    /**
     * @return MockObject&GenericHandler
     */
    private function mockGenericHandler(): mixed
    {
        $this->genericHandlerCalls = [];

        $genericHandler = $this->createMock(
            GenericHandler::class,
        );

        $genericHandler->method('getBoolean')->willReturnCallback(
            function (Element $element, string $field): bool {
                $this->genericHandlerCalls[] = [
                    'method' => 'getBoolean',
                    'element' => $element,
                    'field' => $field,
                ];

                return true;
            }
        );

        $genericHandler->method('getString')->willReturnCallback(
            function (Element $element, string $field): string {
                $this->genericHandlerCalls[] = [
                    'method' => 'getString',
                    'element' => $element,
                    'field' => $field,
                ];

                return 'testString';
            }
        );

        $genericHandler->method('getTwigMarkup')->willReturnCallback(
            function (Element $element, string $field): Markup {
                $this->genericHandlerCalls[] = [
                    'method' => 'getTwigMarkup',
                    'element' => $element,
                    'field' => $field,
                ];

                return new Markup(
                    'testMarkupString',
                    'UTF-8',
                );
            }
        );

        return $genericHandler;
    }

    /**
     * @return MockObject&LinkFieldHandler
     */
    private function mockLinkFieldHandler(): mixed
    {
        $this->linkFieldHandlerCalls = [];

        $linkFieldHandler = $this->createMock(
            LinkFieldHandler::class,
        );

        $linkFieldHandler->method('getModel')->willReturnCallback(
            function (
                Element $element,
                string $field,
            ): LinkFieldModel {
                $this->linkFieldHandlerCalls[] = [
                    'method' => 'getModel',
                    'element' => $element,
                    'field' => $field,
                ];

                $linkFieldModel = $this->createMock(
                    LinkFieldModel::class,
                );

                $linkFieldModel->method('getLink')->willReturn(
                    'testLink',
                );

                return $linkFieldModel;
            }
        );

        return $linkFieldHandler;
    }

    /**
     * @return MockObject&AssetsFieldHandler
     */
    private function mockAssetsFieldHandler(): mixed
    {
        $this->assetsFieldHandlerCalls = [];

        $assetsFieldHandler = $this->createMock(
            AssetsFieldHandler::class,
        );

        $assetsFieldHandler->method('getOne')->willReturnCallback(
            function (
                Element $element,
                string $field,
            ): Asset {
                $this->assetsFieldHandlerCalls[] = [
                    'method' => 'getOne',
                    'element' => $element,
                    'field' => $field,
                ];

                $asset = $this->createMock(Asset::class);

                $asset->title = 'test asset title';

                $asset->method('getUrl')
                    ->willReturn('testAssetUrl');

                return $asset;
            }
        );

        return $assetsFieldHandler;
    }

    /**
     * @return MockObject&ColorOptionFromElementField
     */
    private function mockColorOptionFromElementField(): mixed
    {
        $this->colorOptionFromElementFieldCalls = [];

        $colorOptionFromElementField = $this->createMock(
            ColorOptionFromElementField::class,
        );

        $colorOptionFromElementField->method('getStringValue')
            ->willReturnCallback(
                function (
                    Element $element,
                    string $fieldName,
                    string $option,
                ): string {
                    $this->colorOptionFromElementFieldCalls[] = [
                        'method' => 'getStringValue',
                        'element' => $element,
                        'fieldName' => $fieldName,
                        'option' => $option,
                    ];

                    return 'testColor';
                }
            );

        return $colorOptionFromElementField;
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
            'twigRenderReturn',
            $this->service->buildResponse(
                matrixBlock: $this->matrixBlock,
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
            '@app/Http/PageBuilder/BlockResponse/ImageContentCta/ImageContentCta.twig',
            $this->twigCalls[0]['name'],
        );

        $context = $this->twigCalls[0]['context'];

        self::assertCount(1, $context);

        $contentModel = $context['contentModel'];

        assert($contentModel instanceof ImageContentCtaContentModel);

        self::assertSame(
            'testColor',
            $contentModel->tailwindBackgroundColor(),
        );

        self::assertSame(
            'testString',
            $contentModel->contentDisposition(),
        );

        self::assertSame(
            'testAssetUrl',
            $contentModel->imageUrl(),
        );

        self::assertSame(
            'test asset title',
            $contentModel->imageAltText(),
        );

        self::assertTrue($contentModel->showTealOverlayOnImage());

        self::assertSame(
            'testString',
            $contentModel->preHeadline(),
        );

        self::assertSame(
            'testString',
            $contentModel->headline(),
        );

        self::assertSame(
            'testMarkupString',
            (string) $contentModel->content(),
        );

        self::assertSame(
            'testContent',
            $contentModel->cta()->content(),
        );

        self::assertCount(1, $this->linkFactoryCalls);

        self::assertSame(
            'render',
            $this->linkFactoryCalls[0]['method'],
        );

        self::assertSame(
            'testLink',
            $this->linkFactoryCalls[0]['linkFieldModel']->getLink(),
        );

        self::assertSame(
            [
                [
                    'method' => 'getString',
                    'element' => $this->matrixBlock,
                    'field' => 'contentDisposition',
                ],
                [
                    'method' => 'getBoolean',
                    'element' => $this->matrixBlock,
                    'field' => 'showTealOverlayOnImage',
                ],
                [
                    'method' => 'getString',
                    'element' => $this->matrixBlock,
                    'field' => 'preHeadline',
                ],
                [
                    'method' => 'getString',
                    'element' => $this->matrixBlock,
                    'field' => 'headline',
                ],
                [
                    'method' => 'getTwigMarkup',
                    'element' => $this->matrixBlock,
                    'field' => 'contentField',
                ],
            ],
            $this->genericHandlerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'getModel',
                    'element' => $this->matrixBlock,
                    'field' => 'cta',
                ],
            ],
            $this->linkFieldHandlerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'getOne',
                    'element' => $this->matrixBlock,
                    'field' => 'image',
                ],
            ],
            $this->assetsFieldHandlerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'getStringValue',
                    'element' => $this->matrixBlock,
                    'fieldName' => 'backgroundColor',
                    'option' => 'tailwindColor',
                ],
            ],
            $this->colorOptionFromElementFieldCalls,
        );
    }
}
