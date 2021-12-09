<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\BasicEntryBlock;

use App\Http\Components\Link\Link;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

use function assert;

class BasicEntryBlockTest extends TestCase
{
    use MockTwigForTesting;

    private BasicEntryBlock $block;

    /** @phpstan-ignore-next-line */
    private MatrixBlock $matrixBlock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->block = new BasicEntryBlock(
            twig: $this->mockTwig(),
            genericHandler: $this->mockGenericHandler(),
        );

        $this->matrixBlock =  $this->createMock(
            MatrixBlock::class
        );

        $this->matrixBlock->method('getFieldValue')
            ->willReturnCallback(function (): array {
                return $this->genericCall(
                    object: 'MatrixBlock',
                    return: [
                        [
                            'text' => 'text1',
                            'link' => 'link1',
                        ],
                        [
                            'text' => 'text2',
                            'link' => 'link2',
                        ],
                    ],
                );
            });
    }

    /**
     * @return GenericHandler&MockObject
     */
    private function mockGenericHandler(): GenericHandler|MockObject
    {
        $handler = $this->createMock(
            GenericHandler::class,
        );

        $handler->method('getString')->willReturnCallback(
            function (): string {
                return $this->genericCall(
                    object: 'GenericHandler',
                    return: 'GenericHandlerStringReturn',
                );
            }
        );

        $handler->method('getTwigMarkup')->willReturnCallback(
            function (): Markup {
                return $this->genericCall(
                    object: 'GenericHandler',
                    return: new Markup(
                        'GenericHandlerMarkupReturn',
                        'UTF-8',
                    ),
                );
            }
        );

        return $handler;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     */
    public function testBuildResponse(): void
    {
        $returnString = $this->block->buildResponse(
            matrixBlock: $this->matrixBlock,
        );

        self::assertSame('TwigRender', $returnString);

        self::assertCount(5, $this->calls);

        self::assertSame(
            [
                'object' => 'GenericHandler',
                'method' => 'getString',
                'args' => [
                    $this->matrixBlock,
                    'heading',
                ],
            ],
            $this->calls[0],
        );

        self::assertSame(
            [
                'object' => 'GenericHandler',
                'method' => 'getString',
                'args' => [
                    $this->matrixBlock,
                    'subheading',
                ],
            ],
            $this->calls[1],
        );

        self::assertSame(
            [
                'object' => 'GenericHandler',
                'method' => 'getTwigMarkup',
                'args' => [
                    $this->matrixBlock,
                    'body',
                ],
            ],
            $this->calls[2],
        );

        self::assertSame(
            [
                'object' => 'MatrixBlock',
                'method' => 'getFieldValue',
                'args' => ['callToAction'],
            ],
            $this->calls[3],
        );

        self::assertSame(
            [
                'object' => 'TwigEnvironment',
                'method' => 'render',
            ],
            [
                'object' => $this->calls[4]['object'],
                'method' => $this->calls[4]['method'],
            ]
        );

        $args = $this->calls[4]['args'];

        self::assertCount(2, $args);

        self::assertSame(
            '@app/Http/PageBuilder/BlockResponse/BasicEntryBlock/BasicEntryBlock.twig',
            $args[0],
        );

        $context = $args[1];

        self::assertCount(1, $context);

        $contentModel = $context['contentModel'];

        assert($contentModel instanceof BasicEntryBlockContentModel);

        self::assertSame(
            [
                'headline' => 'GenericHandlerStringReturn',
                'subHeadline' => 'GenericHandlerStringReturn',
                'content' => 'GenericHandlerMarkupReturn',
            ],
            [
                'headline' => $contentModel->headline(),
                'subHeadline' => $contentModel->subHeadline(),
                'content' => (string) $contentModel->content(),
            ],
        );

        self::assertTrue($contentModel->hasCtas());

        self::assertSame(
            [
                [
                    'isEmpty' => false,
                    'content' => 'text1',
                    'href' => 'link1',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'text2',
                    'href' => 'link2',
                    'newWindow' => false,
                ],
            ],
            $contentModel->mapCtas(static fn (Link $link) => [
                'isEmpty' => $link->isEmpty(),
                'content' => $link->content(),
                'href' => $link->href(),
                'newWindow' => $link->newWindow(),
            ]),
        );
    }
}
