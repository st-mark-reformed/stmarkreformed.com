<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\SuperTable;

use craft\base\Element;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use verbb\supertable\elements\db\SuperTableBlockQuery;
use verbb\supertable\elements\SuperTableBlockElement;

use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class SuperTableFieldHandlerTest extends TestCase
{
    private SuperTableFieldHandler $handler;
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
            SuperTableBlockElement::class,
        );
        $blockElement1->title = 'Element Stub 1';

        $blockElement2        = $this->createMock(
            SuperTableBlockElement::class,
        );
        $blockElement2->title = 'Element Stub 2';

        $query = $this->createMock(
            SuperTableBlockQuery::class,
        );

        $query->method('one')->willReturnCallback(
            function () use (
                $blockElement1,
            ): ?SuperTableBlockElement {
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
            ): SuperTableBlockQuery {
                $this->elementCalls[] = [
                    'method' => 'getFieldValue',
                    'fieldHandle' => $fieldHandle,
                ];

                return $query;
            }
        );

        $this->handler = new SuperTableFieldHandler();
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

        assert($result instanceof SuperTableBlockElement);

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
