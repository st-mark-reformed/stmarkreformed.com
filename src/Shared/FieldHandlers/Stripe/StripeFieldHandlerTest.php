<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Stripe;

use craft\base\Element;
use craft\errors\InvalidFieldException;
use enupal\stripe\elements\db\PaymentFormsQuery;
use enupal\stripe\elements\PaymentForm;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class StripeFieldHandlerTest extends TestCase
{
    private StripeFieldHandler $handler;

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

        $element1 = $this->createMock(PaymentForm::class);

        $element1->title = 'Element Stub 1';

        $element2 = $this->createMock(PaymentForm::class);

        $element2->title = 'Element Stub 2';

        $query = $this->createMock(PaymentFormsQuery::class);

        $query->method('one')->willReturnCallback(
            function () use ($element1): ?PaymentForm {
                if ($this->queryOneReturnsElement) {
                    return $element1;
                }

                return null;
            }
        );

        $query->method('all')->willReturn([
            $element1,
            $element2,
        ]);

        $this->element = $this->createMock(Element::class);

        $this->element->method('getFieldValue')->willReturnCallback(
            function (string $fieldHandle) use (
                $query,
            ): PaymentFormsQuery {
                $this->elementCalls[] = [
                    'method' => 'getFieldValue',
                    'fieldHandle' => $fieldHandle,
                ];

                return $query;
            }
        );

        $this->handler = new StripeFieldHandler();
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

        assert($result instanceof PaymentForm);

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
