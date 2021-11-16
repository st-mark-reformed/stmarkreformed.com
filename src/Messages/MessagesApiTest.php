<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Queue\SetMessageSeriesLatestEntryQueueJob;
use App\Messages\RetrieveMessages\MessageRetrieval;
use App\Messages\RetrieveMessages\MessageRetrievalParams;
use App\Messages\RetrieveMessages\MessagesResult;
use App\Messages\SetMessageSeriesLatestEntryDate\SetMessageSeriesLatestEntry;
use craft\queue\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function assert;
use function debug_backtrace;
use function is_array;

/**
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress PossiblyFalseArgument
 * @psalm-suppress PropertyNotSetInConstructor
 */
class MessagesApiTest extends TestCase
{
    private MessagesApi $messagesApi;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->messagesApi = new MessagesApi(
            queue: $this->mockQueue(),
            messageRetrieval: $this->mockMessageRetrieval(),
            setMessageSeriesLatestEntry: $this->mockSetMessageSeriesLatestEntry(),
        );
    }

    /**
     * @psalm-suppress PossiblyUndefinedArrayOffset
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
     * @return Queue&MockObject
     */
    private function mockQueue(): mixed
    {
        $queue = $this->createMock(Queue::class);

        $queue->method(self::anything())->willReturnCallback(
            function (): mixed {
                return $this->genericCall(
                    object: 'Queue',
                    return: true,
                );
            }
        );

        return $queue;
    }

    /**
     * @return MessageRetrieval&MockObject
     */
    private function mockMessageRetrieval(): mixed
    {
        $messageRetrieval = $this->createMock(
            MessageRetrieval::class,
        );

        $messageRetrieval->method('fromParams')->willReturnCallback(
            function (MessageRetrievalParams $params): MessagesResult {
                $this->calls[] = [
                    'object' => 'MessageRetrieval',
                    'method' => 'fromParams',
                    'params' => $params,
                ];

                return new MessagesResult(
                    absoluteTotal: 42,
                    messages: [],
                );
            }
        );

        return $messageRetrieval;
    }

    /**
     * @return SetMessageSeriesLatestEntry&MockObject
     */
    private function mockSetMessageSeriesLatestEntry(): mixed
    {
        $set = $this->createMock(
            SetMessageSeriesLatestEntry::class,
        );

        $set->method(self::anything())->willReturnCallback(
            function (): void {
                $this->genericCall(
                    object: 'SetMessageSeriesLatestEntry',
                );
            }
        );

        return $set;
    }

    public function testRetrieveMessages(): void
    {
        $params = new MessageRetrievalParams();

        $result = $this->messagesApi->retrieveMessages(params: $params);

        self::assertSame(42, $result->absoluteTotal());

        self::assertSame(
            [
                [
                    'object' => 'MessageRetrieval',
                    'method' => 'fromParams',
                    'params' => $params,
                ],
            ],
            $this->calls,
        );
    }

    public function testSetMessageSeriesLatestEntry(): void
    {
        $this->messagesApi->setMessageSeriesLatestEntry();

        self::assertSame(
            [
                [
                    'object' => 'SetMessageSeriesLatestEntry',
                    'method' => 'set',
                    'args' => [],
                ],
            ],
            $this->calls,
        );
    }

    public function testQueueSetMessageSeriesLatestEntry(): void
    {
        $this->messagesApi->queueSetMessageSeriesLatestEntry();

        self::assertCount(1, $this->calls);

        self::assertSame(
            'Queue',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'push',
            $this->calls[0]['method'],
        );

        $args = $this->calls[0]['args'];

        assert(is_array($args));

        self::assertCount(1, $args);

        self::assertInstanceOf(
            SetMessageSeriesLatestEntryQueueJob::class,
            $args[0],
        );
    }
}
