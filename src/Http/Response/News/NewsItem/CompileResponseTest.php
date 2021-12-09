<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsItem;

use App\Http\PageBuilder\PageBuilderResponseCompiler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use App\Shared\Testing\TestCase;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use PHPUnit\Framework\MockObject\MockObject;

class CompileResponseTest extends TestCase
{
    private CompileResponse $compileResponse;

    private Entry $entry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->compileResponse = new CompileResponse(
            matrixFieldHandler: $this->mockMatrixFieldHandler(),
            pageBuilderResponseCompiler: $this->mockPageBuilderResponseCompiler(),
        );

        $this->entry = $this->createMock(Entry::class);
    }

    /**
     * @return MatrixFieldHandler&MockObject
     */
    private function mockMatrixFieldHandler(): MatrixFieldHandler|MockObject
    {
        $block1 = $this->createMock(MatrixBlock::class);

        $block1->title = 'Test Block 1';

        $block2 = $this->createMock(MatrixBlock::class);

        $block2->title = 'Test Block 2';

        $handler = $this->createMock(
            MatrixFieldHandler::class,
        );

        $handler->method('getAll')->willReturnCallback(
            function () use (
                $block1,
                $block2,
            ): array {
                return $this->genericCall(
                    object: 'MatrixFieldHandler',
                    return: [$block1, $block2],
                );
            }
        );

        return $handler;
    }

    /**
     * @return PageBuilderResponseCompiler&MockObject
     */
    private function mockPageBuilderResponseCompiler(): PageBuilderResponseCompiler|MockObject
    {
        $compiler = $this->createMock(
            PageBuilderResponseCompiler::class,
        );

        $compiler->method('compile')->willReturnCallback(
            function (): string {
                return $this->genericCall(
                    object: 'PageBuilderResponseCompiler',
                    return: 'CompilerReturn',
                );
            }
        );

        return $compiler;
    }

    public function testFromEntry(): void
    {
        $responseString = $this->compileResponse->fromEntry(
            entry: $this->entry,
        );

        self::assertSame('CompilerReturn', $responseString);

        self::assertCount(2, $this->calls);

        self::assertSame(
            [
                'object' => 'MatrixFieldHandler',
                'method' => 'getAll',
                'args' => [
                    $this->entry,
                    'entryBuilder',
                ],
            ],
            $this->calls[0],
        );

        self::assertSame(
            [
                'object' => 'PageBuilderResponseCompiler',
                'method' => 'compile',
            ],
            [
                'object' => $this->calls[1]['object'],
                'method' => $this->calls[1]['method'],
            ],
        );

        $args = $this->calls[1]['args'];

        self::assertCount(1, $args);

        $blocks = $args[0];

        self::assertCount(2, $blocks);

        self::assertSame('Test Block 1', $blocks[0]->title);

        self::assertSame('Test Block 2', $blocks[1]->title);
    }
}
