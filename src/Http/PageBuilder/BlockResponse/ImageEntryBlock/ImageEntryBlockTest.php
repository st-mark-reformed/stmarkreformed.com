<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ImageEntryBlock;

use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use craft\elements\Asset;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function assert;

class ImageEntryBlockTest extends TestCase
{
    use MockTwigForTesting;

    private ImageEntryBlock $block;

    /** @phpstan-ignore-next-line */
    private MatrixBlock $matrixBlock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->block = new ImageEntryBlock(
            twig: $this->mockTwig(),
            assetsFieldHandler: $this->mockAssetsFieldHandler(),
        );

        $this->matrixBlock =  $this->createMock(
            MatrixBlock::class
        );
    }

    /**
     * @return AssetsFieldHandler&MockObject
     */
    private function mockAssetsFieldHandler(): AssetsFieldHandler|MockObject
    {
        $asset = $this->createMock(Asset::class);

        $asset->title = 'Asset Title';

        $asset->method('getUrl')->willReturn('/foo/url');

        $handler = $this->createMock(
            AssetsFieldHandler::class,
        );

        $handler->method('getOne')->willReturnCallback(
            function () use ($asset): Asset {
                return $this->genericCall(
                    object: 'AssetsFieldHandler',
                    return: $asset,
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
     * @throws InvalidConfigException
     */
    public function testBuildResponse(): void
    {
        $returnString = $this->block->buildResponse(
            matrixBlock: $this->matrixBlock,
        );

        self::assertSame('TwigRender', $returnString);

        self::assertCount(2, $this->calls);

        self::assertSame(
            [
                'object' => 'AssetsFieldHandler',
                'method' => 'getOne',
                'args' => [
                    $this->matrixBlock,
                    'image',
                ],
            ],
            $this->calls[0],
        );

        self::assertSame(
            [
                'object' => 'TwigEnvironment',
                'method' => 'render',
            ],
            [
                'object' => $this->calls[1]['object'],
                'method' => $this->calls[1]['method'],
            ]
        );

        $args = $this->calls[1]['args'];

        self::assertCount(2, $args);

        self::assertSame(
            '@app/Http/PageBuilder/BlockResponse/ImageEntryBlock/ImageEntryBlock.twig',
            $args[0],
        );

        $context = $args[1];

        self::assertCount(1, $context);

        $contentModel = $context['contentModel'];

        assert($contentModel instanceof ImageEntryBlockContentModel);

        self::assertSame(
            [
                'imageTitle' => 'Asset Title',
                'imageUrl' => '/foo/url',
            ],
            [
                'imageTitle' => $contentModel->imageTitle(),
                'imageUrl' => $contentModel->imageUrl(),
            ],
        );
    }
}
