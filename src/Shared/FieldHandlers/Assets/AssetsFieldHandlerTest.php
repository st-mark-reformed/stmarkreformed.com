<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Assets;

use craft\base\Element;
use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use yii\base\InvalidConfigException;

use function assert;

class AssetsFieldHandlerTest extends TestCase
{
    private AssetsFieldHandler $handler;
    /**
     * @var Element&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $element;

    /** @var mixed[] */
    private array $elementCalls = [];

    private bool $queryOneReturnsElement = true;

    public function setUp(): void
    {
        parent::setUp();

        $this->elementCalls = [];

        $this->queryOneReturnsElement = true;

        $asset1 = $this->createMock(Asset::class);
        $asset1->method('getUrl')->willReturn('testUrl1');

        $asset2 = $this->createMock(Asset::class);
        $asset2->method('getUrl')->willReturn('testUrl2');

        $this->element = $this->createMock(Element::class);

        $query = $this->createMock(AssetQuery::class);

        $query->method('one')->willReturnCallback(
            function () use ($asset1): ?Asset {
                if ($this->queryOneReturnsElement) {
                    return $asset1;
                }

                return null;
            }
        );

        $query->method('all')->willReturn([
            $asset1,
            $asset2,
        ]);

        $this->element->method('getFieldValue')->willReturnCallback(
            function (string $fieldHandle) use (
                $query,
            ): AssetQuery {
                $this->elementCalls[] = [
                    'method' => 'getFieldValue',
                    'fieldHandle' => $fieldHandle,
                ];

                return $query;
            }
        );

        $this->handler = new AssetsFieldHandler();
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testGetAll(): void
    {
        $result = $this->handler->getAll(
            element: $this->element,
            field: 'testField',
        );

        self::assertCount(2, $result);

        self::assertSame(
            'testUrl1',
            $result[0]->getUrl(),
        );

        self::assertSame(
            'testUrl2',
            $result[1]->getUrl(),
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
     * @throws InvalidConfigException
     */
    public function testGetOneOrNullWhenHasOne(): void
    {
        $this->queryOneReturnsElement = true;

        $result = $this->handler->getOneOrNull(
            element: $this->element,
            field: 'testField',
        );

        assert($result instanceof Asset);

        self::assertSame(
            'testUrl1',
            $result->getUrl(),
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
     * @throws InvalidConfigException
     */
    public function testGetOne(): void
    {
        $this->queryOneReturnsElement = true;

        $result = $this->handler->getOne(
            element: $this->element,
            field: 'testField',
        );

        self::assertSame(
            'testUrl1',
            $result->getUrl(),
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
