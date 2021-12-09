<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\EntryBuilder;

use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use App\Shared\Testing\TestCase;
use craft\base\Element;
use craft\elements\MatrixBlock;
use craft\models\MatrixBlockType;
use PHPUnit\Framework\MockObject\MockObject;

class ExtractBodyContentTest extends TestCase
{
    private ExtractBodyContent $extractBodyContent;

    /** @phpstan-ignore-next-line */
    private MatrixBlock $matrixBlock1;

    /** @phpstan-ignore-next-line */
    private MatrixBlock $matrixBlock2;

    /** @phpstan-ignore-next-line */
    private MatrixBlock $matrixBlock3;

    /** @phpstan-ignore-next-line */
    private Element $element;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractBodyContent = new ExtractBodyContent(
            genericHandler: $this->mockGenericHandler(),
            matrixFieldHandler: $this->mockMatrixFieldHandler(),
        );

        $this->element = $this->createMock(Element::class);
    }

    /**
     * @return GenericHandler&MockObject
     */
    private function mockGenericHandler(): GenericHandler|MockObject
    {
        $handler = $this->createMock(GenericHandler::class);

        $handler->method('getString')->willReturnCallback(
            function (): string {
                return $this->genericCall(
                    object: 'GenericHandler',
                    return: 'GenericHandlerGetString',
                );
            }
        );

        return $handler;
    }

    /**
     * @return MatrixFieldHandler&MockObject
     */
    private function mockMatrixFieldHandler(): MatrixFieldHandler|MockObject
    {
        $otherType = $this->createMock(
            MatrixBlockType::class,
        );

        $otherType->handle = 'other';

        $basicEntryBlockType = $this->createMock(
            MatrixBlockType::class,
        );

        $basicEntryBlockType->handle = 'basicEntryBlock';

        $this->matrixBlock1 = $this->createMock(
            MatrixBlock::class,
        );

        $this->matrixBlock1->method('getType')->willReturn(
            $basicEntryBlockType
        );

        $this->matrixBlock2 = $this->createMock(
            MatrixBlock::class,
        );

        $this->matrixBlock2->method('getType')->willReturn(
            $otherType
        );

        $this->matrixBlock3 = $this->createMock(
            MatrixBlock::class,
        );

        $this->matrixBlock3->method('getType')->willReturn(
            $basicEntryBlockType
        );

        $handler = $this->createMock(
            MatrixFieldHandler::class,
        );

        $handler->method('getAll')->willReturnCallback(
            function (): array {
                return $this->genericCall(
                    object: 'MatrixFieldHandler',
                    return: [
                        $this->matrixBlock1,
                        $this->matrixBlock2,
                        $this->matrixBlock3,
                    ],
                );
            }
        );

        return $handler;
    }

    public function testFromElementWithEntryBuilder(): void
    {
        $value = $this->extractBodyContent->fromElementWithEntryBuilder(
            element: $this->element,
        );

        self::assertSame(
            'GenericHandlerGetStringGenericHandlerGetString',
            $value,
        );

        self::assertSame(
            [
                [
                    'object' => 'MatrixFieldHandler',
                    'method' => 'getAll',
                    'args' => [
                        $this->element,
                        'entryBuilder',
                    ],
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'args' => [
                        $this->matrixBlock1,
                        'body',
                    ],
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'args' => [
                        $this->matrixBlock3,
                        'body',
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
