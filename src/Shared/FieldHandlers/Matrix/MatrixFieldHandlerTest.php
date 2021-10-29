<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Matrix;

use craft\base\Element;
use craft\elements\db\MatrixBlockQuery;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class MatrixFieldHandlerTest extends TestCase
{
    private MatrixFieldHandler $handler;

    /**
     * @var Element&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $element;

    /** @var mixed[] */
    private array $elementCalls = [];

    private bool $queryOneReturnsElement = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->elementCalls = [];

        $this->queryOneReturnsElement = true;

        $blockElement1        = $this->createMock(
            MatrixBlock::class
        );
        $blockElement1->title = 'Element Stub 1';

        $blockElement2        = $this->createMock(
            MatrixBlock::class
        );
        $blockElement2->title = 'Element Stub 2';

        $query = $this->createMock(
            MatrixBlockQuery::class,
        );

        $query->method('one')->willReturnCallback(
            function () use (
                $blockElement1,
            ): ?MatrixBlock {
                if ($this->queryOneReturnsElement) {
                    return $blockElement1;
                }

                return null;
            }
        );

        $query->method('all')->willReturn([
            $blockElement1,
            $blockElement2,
        ]);

        $this->element = $this->createMock(Element::class);

        $this->element->method('getFieldValue')->willReturnCallback(
            function (string $fieldHandle) use (
                $query,
            ): MatrixBlockQuery {
                $this->elementCalls[] = [
                    'method' => 'getFieldValue',
                    'fieldHandle' => $fieldHandle,
                ];

                return $query;
            }
        );

        $this->handler = new MatrixFieldHandler();
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetAll(): void
    {
        $result = $this->handler->getAll(
            element: $this->element,
            field: 'testField',
        );

        self::assertCount(2, $result);

        self::assertSame(
            'Element Stub 1',
            $result[0]->title,
        );

        self::assertSame(
            'Element Stub 2',
            $result[1]->title,
        );

        self::assertCount(1, $this->elementCalls);

        self::assertSame(
            'getFieldValue',
            $this->elementCalls[0]['method'],
        );

        self::assertSame(
            'testField',
            $this->elementCalls[0]['fieldHandle'],
        );
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetOneOrNullWhenHasOne(): void
    {
        $this->queryOneReturnsElement = true;

        $result = $this->handler->getOneOrNull(
            element: $this->element,
            field: 'testField',
        );

        assert($result instanceof MatrixBlock);

        self::assertSame(
            'Element Stub 1',
            $result->title,
        );

        self::assertCount(1, $this->elementCalls);

        self::assertSame(
            'getFieldValue',
            $this->elementCalls[0]['method'],
        );

        self::assertSame(
            'testField',
            $this->elementCalls[0]['fieldHandle'],
        );
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetOneOrNullWhenDoesNotHaveOne(): void
    {
        $this->queryOneReturnsElement = false;

        $result = $this->handler->getOneOrNull(
            element: $this->element,
            field: 'testField',
        );

        assert($result === null);

        self::assertCount(1, $this->elementCalls);

        self::assertSame(
            'getFieldValue',
            $this->elementCalls[0]['method'],
        );

        self::assertSame(
            'testField',
            $this->elementCalls[0]['fieldHandle'],
        );
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetOne(): void
    {
        $this->queryOneReturnsElement = true;

        $result = $this->handler->getOne(
            element: $this->element,
            field: 'testField',
        );

        self::assertSame(
            'Element Stub 1',
            $result->title,
        );

        self::assertCount(1, $this->elementCalls);

        self::assertSame(
            'getFieldValue',
            $this->elementCalls[0]['method'],
        );

        self::assertSame(
            'testField',
            $this->elementCalls[0]['fieldHandle'],
        );
    }
}
