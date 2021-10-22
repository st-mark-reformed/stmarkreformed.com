<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\BlockNotImplemented;

use craft\elements\MatrixBlock;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
     */
    public function testBuildResponse(): void
    {
        $twigSpy = $this->createMock(TwigEnvironment::class);

        $twigSpy->expects(self::once())
            ->method('render')
            ->with(self::equalTo(
                '@app/Http/PageBuilder/BlockResponse/BlockNotImplemented/BlockNotImplemented.twig',
            ))
            ->willReturn('testTwigResponse');

        $service = new BlockNotImplemented(twig: $twigSpy);

        self::assertSame(
            'testTwigResponse',
            $service->buildResponse($this->createMock(
                MatrixBlock::class,
            )),
        );
    }
}
