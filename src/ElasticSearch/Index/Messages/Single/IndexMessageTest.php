<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\Single;

use craft\elements\Entry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class IndexMessageTest extends TestCase
{
    private IndexMessage $indexMessage;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->indexMessage = new IndexMessage(
            factory: $this->mockIndexMessageEntryFactory(),
        );
    }

    /**
     * @return IndexMessageEntryFactory&MockObject
     */
    private function mockIndexMessageEntryFactory(): mixed
    {
        $indexMessageEntryFactory = $this->createMock(
            IndexMessageEntryFactory::class,
        );

        $indexMessageEntryFactory->method('make')
            ->willReturnCallback(
                function (
                    Entry $message,
                ): IndexMessageEntryContract {
                    $this->calls[] = [
                        'object' => 'IndexMessageEntryFactory',
                        'method' => 'make',
                        'message' => $message,
                    ];

                    return $this->mockIndexMessageEntryContract();
                }
            );

        return $indexMessageEntryFactory;
    }

    /**
     * @return IndexMessageEntryContract&MockObject
     */
    private function mockIndexMessageEntryContract(): mixed
    {
        $indexMessageEntryContract = $this->createMock(
            IndexMessageEntryContract::class,
        );

        $indexMessageEntryContract->method('index')
            ->willReturnCallback(
                function (Entry $message): void {
                    $this->calls[] = [
                        'object' => 'IndexMessageEntryContract',
                        'method' => 'index',
                        'message' => $message,
                    ];
                }
            );

        return $indexMessageEntryContract;
    }

    public function testIndex(): void
    {
        $message = $this->createMock(Entry::class);

        $this->indexMessage->index(message: $message);

        self::assertSame(
            [
                [
                    'object' => 'IndexMessageEntryFactory',
                    'method' => 'make',
                    'message' => $message,
                ],
                [
                    'object' => 'IndexMessageEntryContract',
                    'method' => 'index',
                    'message' => $message,
                ],
            ],
            $this->calls,
        );
    }
}
