<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Generic;

use craft\base\Element;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Markup;

class GenericHandlerTest extends TestCase
{
    private GenericHandler $handler;
    /**
     * @var Element&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $element;

    /** @var mixed[] */
    private array $elementCalls = [];

    private mixed $fieldReturn = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->elementCalls = [];

        $this->fieldReturn = null;

        $this->element = $this->createMock(Element::class);

        $this->element->method('getFieldValue')->willReturnCallback(
            function (string $fieldHandle): mixed {
                $this->elementCalls[] = [
                    'method' => 'getFieldValue',
                    'fieldHandle' => $fieldHandle,
                ];

                return $this->fieldReturn;
            }
        );

        $this->handler = new GenericHandler();
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetTwigMarkupWhenNull(): void
    {
        $this->fieldReturn = null;

        $result = $this->handler->getTwigMarkup(
            element: $this->element,
            field: 'someFieldHandle',
        );

        self::assertInstanceOf(Markup::class, $result);

        self::assertSame('', (string) $result);

        self::assertCount(1, $this->elementCalls);

        self::assertSame(
            'getFieldValue',
            $this->elementCalls[0]['method'],
        );

        self::assertSame(
            'someFieldHandle',
            $this->elementCalls[0]['fieldHandle'],
        );
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetTwigMarkup(): void
    {
        $returnMarkup = new Markup('test', 'UTF-8');

        $this->fieldReturn = $returnMarkup;

        $result = $this->handler->getTwigMarkup(
            element: $this->element,
            field: 'someFieldHandle',
        );

        self::assertSame($returnMarkup, $result);

        self::assertCount(1, $this->elementCalls);

        self::assertSame(
            'getFieldValue',
            $this->elementCalls[0]['method'],
        );

        self::assertSame(
            'someFieldHandle',
            $this->elementCalls[0]['fieldHandle'],
        );
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetString(): void
    {
        $returnString = 'testReturnString';

        $this->fieldReturn = $returnString;

        $result = $this->handler->getString(
            element: $this->element,
            field: 'aFieldHandle',
        );

        self::assertSame($returnString, $result);

        self::assertCount(1, $this->elementCalls);

        self::assertSame(
            'getFieldValue',
            $this->elementCalls[0]['method'],
        );

        self::assertSame(
            'aFieldHandle',
            $this->elementCalls[0]['fieldHandle'],
        );
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetInt(): void
    {
        $returnInt = '456';

        $this->fieldReturn = $returnInt;

        $result = $this->handler->getInt(
            element: $this->element,
            field: 'aFieldHandle',
        );

        self::assertSame(((int) $returnInt), $result);

        self::assertCount(1, $this->elementCalls);

        self::assertSame(
            'getFieldValue',
            $this->elementCalls[0]['method'],
        );

        self::assertSame(
            'aFieldHandle',
            $this->elementCalls[0]['fieldHandle'],
        );
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetBoolean(): void
    {
        $returnString = '1';

        $this->fieldReturn = $returnString;

        $result = $this->handler->getBoolean(
            element: $this->element,
            field: 'fieldHandle',
        );

        self::assertTrue($result);

        self::assertCount(1, $this->elementCalls);

        self::assertSame(
            'getFieldValue',
            $this->elementCalls[0]['method'],
        );

        self::assertSame(
            'fieldHandle',
            $this->elementCalls[0]['fieldHandle'],
        );
    }
}
