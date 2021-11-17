<?php

declare(strict_types=1);

namespace App\Messages\SetMessageSeriesLatestEntryDate\SetFromEntry;

use craft\elements\Category;
use craft\errors\ElementNotFoundException;
use craft\services\Elements as ElementsService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;
use yii\base\Exception;

use function debug_backtrace;

/**
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress PossiblyFalseArgument
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedInferredReturnType
 */
class SetToNullTest extends TestCase
{
    private SetToNull $service;

    /** @var mixed[] */
    private array $calls = [];

    /**
     * @var Category&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $series;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->service = new SetToNull(
            elementsService: $this->mockElementsService(),
        );

        $this->series = $this->mockSeries();
    }

    /**
     * @param R $return
     *
     * @return R
     *
     * @template R
     * @psalm-suppress PossiblyUndefinedArrayOffset
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    private function genericCall(
        string $object,
        mixed $return = null
    ): mixed {
        $trace = debug_backtrace()[5];

        $this->calls[] = [
            'object' => $object,
            'method' => $trace['function'],
            'args' => $trace['args'],
        ];

        return $return;
    }

    /**
     * @return ElementsService&MockObject
     */
    private function mockElementsService(): mixed
    {
        $service = $this->createMock(ElementsService::class);

        $service->method(self::anything())->willReturnCallback(
            function (): bool {
                return $this->genericCall(
                    object: 'ElementsService',
                    return: true,
                );
            }
        );

        return $service;
    }

    /**
     * @return Category&MockObject
     *
     * @phpstan-ignore-next-line
     */
    private function mockSeries(): mixed
    {
        $series = $this->createMock(Category::class);

        $series->method(self::anything())->willReturnCallback(
            function (): void {
                $this->genericCall(object: 'Category');
            }
        );

        return $series;
    }

    /**
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function testSet(): void
    {
        $this->service->set($this->series, null);

        self::assertSame(
            [
                [
                    'object' => 'Category',
                    'method' => 'setFieldValue',
                    'args' => [
                        'latestEntryAt',
                        null,
                    ],
                ],
                [
                    'object' => 'ElementsService',
                    'method' => 'saveElement',
                    'args' => [$this->series],
                ],
            ],
            $this->calls,
        );
    }
}
