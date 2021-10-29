<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\BasicBlock;

use App\Http\Components\Link\Link;
use App\Http\Components\Link\LinkFactory;
use App\Shared\FieldHandlers\ColourSwatches\ColorOptionFromElementField;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\LinkField\LinkFieldHandler;
use App\Shared\FieldHandlers\SuperTable\SuperTableFieldHandler;
use craft\base\Element;
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
use verbb\supertable\elements\SuperTableBlockElement;

use function assert;
use function count;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedMethodCall
 */
class BasicBlockTest extends TestCase
{
    private BasicBlock $service;

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
    private array $superTableFieldHandlerCalls = [];

    /** @var mixed[] */
    private array $colorOptionFromElementFieldCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockMatrixBlock();

        $this->service = new BasicBlock(
            twig: $this->mockTwig(),
            linkFactory: $this->mockLinkFactory(),
            genericHandler: $this->mockGenericHandler(),
            linkFieldHandler: $this->mockLinkFieldHandler(),
            superTableFieldHandler: $this->mockSuperTableFieldHandler(),
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
                        content: 'test' . count($this->linkFactoryCalls),
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
                    'testTwigMarkupString',
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
                    'testLink' . count($this->linkFieldHandlerCalls),
                );

                return $linkFieldModel;
            }
        );

        return $linkFieldHandler;
    }

    /**
     * @return MockObject&SuperTableFieldHandler
     */
    private function mockSuperTableFieldHandler(): mixed
    {
        $this->superTableFieldHandlerCalls = [];

        $superTableFieldHandler = $this->createMock(
            SuperTableFieldHandler::class,
        );

        $superTableFieldHandler->method('getAll')
            ->willReturnCallback(
                function (
                    Element $element,
                    string $field,
                ): array {
                    $this->superTableFieldHandlerCalls[] = [
                        'method' => 'getAll',
                        'element' => $element,
                        'field' => $field,
                    ];

                    $block1 = $this->createMock(
                        SuperTableBlockElement::class,
                    );

                    $block1->method('getUrl')->willReturn(
                        'block1',
                    );

                    $block2 = $this->createMock(
                        SuperTableBlockElement::class,
                    );

                    $block2->method('getUrl')->willReturn(
                        'block2',
                    );

                    return [
                        $block1,
                        $block2,
                    ];
                }
            );

        return $superTableFieldHandler;
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
            '@app/Http/PageBuilder/BlockResponse/BasicBlock/BasicBlock.twig',
            $this->twigCalls[0]['name'],
        );

        $context = $this->twigCalls[0]['context'];

        self::assertCount(1, $context);

        $contentModel = $context['contentModel'];

        assert($contentModel instanceof BasicBlockContentModel);

        self::assertSame(
            'testColor',
            $contentModel->tailwindBackgroundColor(),
        );

        self::assertSame(
            'testString',
            $contentModel->alignment(),
        );

        self::assertSame(
            'testString',
            $contentModel->preHeadline(),
        );

        self::assertSame(
            'testString',
            $contentModel->headline(),
        );

        self::assertSame(
            'testTwigMarkupString',
            (string) $contentModel->content(),
        );

        self::assertTrue($contentModel->hasCtas());

        $ctas = $contentModel->ctas();

        self::assertCount(2, $ctas);

        self::assertSame('test1', $ctas[0]->content());

        self::assertSame('test2', $ctas[1]->content());

        self::assertCount(
            2,
            $this->linkFactoryCalls,
        );

        self::assertSame(
            'render',
            $this->linkFactoryCalls[0]['method'],
        );

        self::assertSame(
            'testLink1',
            $this->linkFactoryCalls[0]['linkFieldModel']->getLink(),
        );

        self::assertSame(
            'render',
            $this->linkFactoryCalls[1]['method'],
        );

        self::assertSame(
            'testLink2',
            $this->linkFactoryCalls[1]['linkFieldModel']->getLink(),
        );

        self::assertSame(
            [
                [
                    'method' => 'getString',
                    'element' => $this->matrixBlock,
                    'field' => 'alignment',
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

        self::assertCount(
            2,
            $this->linkFieldHandlerCalls,
        );

        self::assertSame(
            'getModel',
            $this->linkFieldHandlerCalls[0]['method'],
        );

        self::assertSame(
            'block1',
            $this->linkFieldHandlerCalls[0]['element']->getUrl(),
        );

        self::assertSame(
            'urlField',
            $this->linkFieldHandlerCalls[0]['field'],
        );

        self::assertSame(
            'getModel',
            $this->linkFieldHandlerCalls[1]['method'],
        );

        self::assertSame(
            'block2',
            $this->linkFieldHandlerCalls[1]['element']->getUrl(),
        );

        self::assertSame(
            'urlField',
            $this->linkFieldHandlerCalls[1]['field'],
        );

        self::assertSame(
            [
                [
                    'method' => 'getAll',
                    'element' => $this->matrixBlock,
                    'field' => 'ctas',
                ],
            ],
            $this->superTableFieldHandlerCalls,
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
