<?php

declare(strict_types=1);

namespace App\Messages\SetMessageSeriesLatestEntryDate\SetFromEntry;

use craft\elements\Category;
use craft\elements\Entry;
use craft\errors\ElementNotFoundException;
use craft\services\Elements as ElementsService;
use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;
use yii\base\Exception;

use function assert;
use function debug_backtrace;

/**
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress PossiblyFalseArgument
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedInferredReturnType
 */
class SetFromMessageTest extends TestCase
{
    private SetFromMessage $service;

    /** @var mixed[] */
    private array $calls = [];

    /**
     * @var Category&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $series;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->service = new SetFromMessage(
            elementsService: $this->mockElementsService(),
        );

        $this->series = $this->mockSeries();

        $this->message = $this->mockMessage();
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
     * @return Entry&MockObject
     *
     * @phpstan-ignore-next-line
     */
    private function mockMessage(): mixed
    {
        $message = $this->createMock(Entry::class);

        $postDate = DateTime::createFromFormat(
            DateTimeInterface::ATOM,
            '1982-01-27T10:00:10+00:00'
        );

        assert($postDate instanceof DateTime);

        $message->postDate = $postDate;

        return $message;
    }

    /**
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function testSet(): void
    {
        $this->service->set($this->series, $this->message);

        self::assertSame(
            [
                [
                    'object' => 'Category',
                    'method' => 'setFieldValue',
                    'args' => [
                        'latestEntryAt',
                        $this->message->postDate,
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
