<?php

declare(strict_types=1);

namespace App\Messages\SetMessageSeriesLatestEntryDate;

use App\Messages\SetMessageSeriesLatestEntryDate\SetFromEntry\SetFromMessageContract;
use App\Messages\SetMessageSeriesLatestEntryDate\SetFromEntry\SetFromMessageFactory;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Category;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function debug_backtrace;

/**
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress PossiblyFalseArgument
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedInferredReturnType
 */
class SetLatestMessageForSeriesTest extends TestCase
{
    private SetLatestMessageForSeries $service;

    /** @var mixed[] */
    private array $calls = [];

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->message = $this->createMock(Entry::class);

        $this->service = new SetLatestMessageForSeries(
            entryQueryFactory: $this->mockEntryQueryFactory(),
            setFromEntryFactory: $this->mockSetFromEntryFactory(),
        );
    }

    /**
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
     * @return EntryQueryFactory&MockObject
     */
    private function mockEntryQueryFactory(): mixed
    {
        $query = $this->createMock(
            EntryQuery::class,
        );

        $query->method('section')->willReturnCallback(
            function () use ($query): EntryQuery {
                return $this->genericCall(
                    object: 'EntryQueryFactory',
                    return: $query,
                );
            }
        );

        $query->method('relatedTo')->willReturnCallback(
            function () use ($query): EntryQuery {
                return $this->genericCall(
                    object: 'EntryQueryFactory',
                    return: $query,
                );
            }
        );

        $query->method('one')->willReturnCallback(
            function (): Entry {
                return $this->genericCall(
                    object: 'EntryQueryFactory',
                    return: $this->message,
                );
            }
        );

        $factory = $this->createMock(
            EntryQueryFactory::class,
        );

        $factory->method('make')->willReturn($query);

        return $factory;
    }

    /**
     * @return SetFromMessageFactory&MockObject
     */
    private function mockSetFromEntryFactory(): mixed
    {
        $setFromMessageContract = $this->createMock(
            SetFromMessageContract::class,
        );

        $setFromMessageContract->method('set')->willReturnCallback(
            function (): void {
                $this->genericCall(object: 'SetFromMessageContract');
            }
        );

        $factory = $this->createMock(
            SetFromMessageFactory::class,
        );

        $factory->method('make')->willReturnCallback(
            function () use (
                $setFromMessageContract,
            ): SetFromMessageContract {
                $this->genericCall(
                    object: 'SetFromMessageFactory',
                    return: $setFromMessageContract,
                );

                return $setFromMessageContract;
            }
        );

        return $factory;
    }

    public function testSet(): void
    {
        $series = $this->createMock(Category::class);

        $this->service->set(series: $series);

        self::assertSame(
            [
                [
                    'object' => 'EntryQueryFactory',
                    'method' => 'section',
                    'args' => ['messages'],
                ],
                [

                    'object' => 'EntryQueryFactory',
                    'method' => 'relatedTo',
                    'args' => [
                        [
                            'targetElement' => $series,
                            'field' => 'messageSeries',
                        ],
                    ],
                ],
                [
                    'object' => 'EntryQueryFactory',
                    'method' => 'one',
                    'args' => [],
                ],
                [
                    'object' => 'SetFromMessageFactory',
                    'method' => 'make',
                    'args' => [
                        $this->message,
                    ],
                ],
                [
                    'object' => 'SetFromMessageContract',
                    'method' => 'set',
                    'args' => [
                        $series,
                        $this->message,
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
