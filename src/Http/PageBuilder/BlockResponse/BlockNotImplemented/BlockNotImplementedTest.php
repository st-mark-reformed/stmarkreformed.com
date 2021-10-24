<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\BlockNotImplemented;

use craft\elements\MatrixBlock;
use craft\models\MatrixBlockType;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class BlockNotImplementedTest extends TestCase
{
    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidConfigException
     */
    public function testBuildResponse(): void
    {
        $matrixBlockTypeStub = $this->createMock(
            MatrixBlockType::class,
        );

        $matrixBlockTypeStub->handle = 'fooHandle';

        $matrixBlockStub = $this->createMock(
            MatrixBlock::class,
        );

        $matrixBlockStub->method('getType')->willReturn(
            $matrixBlockTypeStub,
        );

        $twigSpy = $this->createMock(TwigEnvironment::class);

        $twigSpy->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo(
                    '@app/Http/PageBuilder/BlockResponse/BlockNotImplemented/BlockNotImplemented.twig',
                ),
                self::equalTo(['blockType' => 'fooHandle']),
            )
            ->willReturn('testTwigResponse');

        $service = new BlockNotImplemented(twig: $twigSpy);

        self::assertSame(
            'testTwigResponse',
            $service->buildResponse(matrixBlock: $matrixBlockStub),
        );
    }
}
