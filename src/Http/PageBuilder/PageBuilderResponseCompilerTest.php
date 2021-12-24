<?php

declare(strict_types=1);

namespace App\Http\PageBuilder;

use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderFactory;
use craft\elements\MatrixBlock;
use PHPUnit\Framework\TestCase;

use function count;

use const PHP_EOL;

class PageBuilderResponseCompilerTest extends TestCase
{
    /** @var mixed[] */
    private array $factoryCalls = [];

    /** @var mixed[] */
    private array $buildCalls = [];

    public function testMake(): void
    {
        $pageBuilderBlock1Stub = $this->createMock(
            MatrixBlock::class
        );

        $pageBuilderBlock2Stub = $this->createMock(
            MatrixBlock::class
        );

        $blockResponseBuilderStub = $this->createMock(
            BlockResponseBuilderContract::class,
        );

        $blockResponseBuilderStub->method('buildResponse')
            ->willReturnCallback(
                function (MatrixBlock $block,): string {
                    $this->buildCalls[] = [
                        'method' => 'buildResponse',
                        'block' => $block,
                    ];

                    return 'Response' . count($this->factoryCalls) . PHP_EOL;
                }
            );

        $blockResponseBuilderFactoryStub = $this->createMock(
            BlockResponseBuilderFactory::class,
        );

        $blockResponseBuilderFactoryStub->method('make')
            ->willReturnCallback(
                function (
                    MatrixBlock $block,
                ) use (
                    $blockResponseBuilderStub,
                ): BlockResponseBuilderContract {
                    $this->factoryCalls[] = [
                        'method' => 'make',
                        'block' => $block,
                    ];

                    return $blockResponseBuilderStub;
                }
            );

        $compiler = new PageBuilderResponseCompiler(
            blockResponseBuilderFactory: $blockResponseBuilderFactoryStub,
        );

        self::assertSame(
            'Response1' . PHP_EOL .
                'Response2' . PHP_EOL,
            $compiler->compile([
                $pageBuilderBlock1Stub,
                $pageBuilderBlock2Stub,
            ]),
        );

        self::assertCount(2, $this->factoryCalls);

        self::assertSame(
            'make',
            $this->factoryCalls[0]['method'],
        );

        self::assertSame(
            $pageBuilderBlock1Stub,
            $this->factoryCalls[0]['block'],
        );

        self::assertSame(
            'make',
            $this->factoryCalls[1]['method'],
        );

        self::assertSame(
            $pageBuilderBlock2Stub,
            $this->factoryCalls[1]['block'],
        );

        self::assertCount(2, $this->buildCalls);

        self::assertSame(
            'buildResponse',
            $this->buildCalls[0]['method'],
        );

        self::assertSame(
            $pageBuilderBlock1Stub,
            $this->buildCalls[0]['block'],
        );

        self::assertSame(
            'buildResponse',
            $this->buildCalls[1]['method'],
        );

        self::assertSame(
            $pageBuilderBlock2Stub,
            $this->buildCalls[1]['block'],
        );
    }
}
