<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\LinkField;

use craft\base\Element;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use typedlinkfield\models\Link as LinkFieldModel;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class LinkFieldHandlerTest extends TestCase
{
    private LinkFieldHandler $handler;
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

        $linkFieldModel = $this->createMock(
            LinkFieldModel::class,
        );

        $linkFieldModel->method('getLink')->willReturn(
            'testLink',
        );

        $this->element = $this->createMock(Element::class);

        $this->element->method('getFieldValue')->willReturnCallback(
            function (string $fieldHandle) use (
                $linkFieldModel,
            ): LinkFieldModel {
                $this->elementCalls[] = [
                    'method' => 'getFieldValue',
                    'fieldHandle' => $fieldHandle,
                ];

                return $linkFieldModel;
            }
        );

        $this->handler = new LinkFieldHandler();
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetModel(): void
    {
        $result = $this->handler->getModel(
            element: $this->element,
            field: 'testFieldHandle',
        );

        self::assertSame(
            'testLink',
            $result->getLink(),
        );

        self::assertCount(1, $this->elementCalls);

        self::assertSame(
            'getFieldValue',
            $this->elementCalls[0]['method'],
        );

        self::assertSame(
            'testFieldHandle',
            $this->elementCalls[0]['fieldHandle'],
        );
    }
}
