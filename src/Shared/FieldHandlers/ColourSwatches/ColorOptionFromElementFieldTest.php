<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\ColourSwatches;

use craft\base\Element;
use craft\errors\InvalidFieldException;
use percipioglobal\colourswatches\models\ColourSwatches;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class ColorOptionFromElementFieldTest extends TestCase
{
    private ColorOptionFromElementField $handler;
    /**
     * @var Element&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $element;

    /** @var mixed[] */
    private array $elementCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->elementCalls = [];

        $colorsStdClass                  = new stdClass();
        $colorsStdClass->testOptionParam = 'testOptionString';

        $colors = [$colorsStdClass];

        $colorModel = $this->createMock(
            ColourSwatches::class,
        );

        $colorModel->method('colors')->willReturn($colors);

        $this->element = $this->createMock(Element::class);

        $this->element->method('getFieldValue')->willReturnCallback(
            function (string $fieldHandle) use (
                $colorModel,
            ): ColourSwatches {
                $this->elementCalls[] = [
                    'method' => 'getFieldValue',
                    'fieldHandle' => $fieldHandle,
                ];

                return $colorModel;
            }
        );

        $this->handler = new ColorOptionFromElementField();
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetStringValue(): void
    {
        $result = $this->handler->getStringValue(
            element: $this->element,
            fieldName: 'someFieldName',
            option: 'testOptionParam',
        );

        self::assertSame(
            'testOptionString',
            $result,
        );

        self::assertCount(1, $this->elementCalls);

        self::assertSame(
            'getFieldValue',
            $this->elementCalls[0]['method'],
        );

        self::assertSame(
            'someFieldName',
            $this->elementCalls[0]['fieldHandle'],
        );
    }
}
