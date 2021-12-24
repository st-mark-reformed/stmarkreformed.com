<?php

declare(strict_types=1);

namespace App\Messages\SetMessageSeriesLatestEntryDate\SetFromEntry;

use App\Shared\Testing\TestCase;
use craft\elements\Category;
use craft\errors\ElementNotFoundException;
use craft\services\Elements as ElementsService;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;
use yii\base\Exception;

class SetToNullTest extends TestCase
{
    private SetToNull $service;

    /**
     * @var Category&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $series;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new SetToNull(
            elementsService: $this->mockElementsService(),
        );

        $this->series = $this->mockSeries();
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
