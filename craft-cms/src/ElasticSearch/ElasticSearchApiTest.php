<?php

declare(strict_types=1);

namespace App\ElasticSearch;

use App\ElasticSearch\Index\Messages\All\IndexAllMessages;
use App\ElasticSearch\Index\Messages\Single\IndexMessage;
use App\ElasticSearch\Queue\IndexAllMessagesQueueJob;
use App\ElasticSearch\SetUpIndices\SetUpIndices;
use craft\elements\Entry;
use craft\queue\BaseJob;
use craft\queue\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ElasticSearchApiTest extends TestCase
{
    private ElasticSearchApi $api;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->api = new ElasticSearchApi(
            queue: $this->mockQueue(),
            setUpIndices: $this->mockSetUpIndices(),
            indexMessage: $this->mockIndexMessage(),
            indexAllMessages: $this->mockIndexAllMessages(),
        );
    }

    /**
     * @return Queue&MockObject
     */
    private function mockQueue(): mixed
    {
        $queue = $this->createMock(Queue::class);

        $queue->method('push')->willReturnCallback(
            function (BaseJob $job): string {
                $this->calls[] = [
                    'object' => 'Queue',
                    'method' => 'push',
                    'job' => $job,
                ];

                return 'fooBar';
            }
        );

        return $queue;
    }

    /**
     * @return SetUpIndices&MockObject
     */
    private function mockSetUpIndices(): mixed
    {
        $setUpIndices = $this->createMock(
            SetUpIndices::class,
        );

        $setUpIndices->method('setUp')->willReturnCallback(
            function (): void {
                $this->calls[] = [
                    'object' => 'SetUpIndices',
                    'method' => 'setUp',
                ];
            }
        );

        return $setUpIndices;
    }

    /**
     * @return IndexMessage&MockObject
     */
    private function mockIndexMessage(): mixed
    {
        $indexMessage = $this->createMock(
            IndexMessage::class,
        );

        $indexMessage->method('index')->willReturnCallback(
            function (Entry $message): void {
                $this->calls[] = [
                    'object' => 'IndexMessage',
                    'method' => 'index',
                    'message' => $message,
                ];
            }
        );

        return $indexMessage;
    }

    /**
     * @return IndexAllMessages&MockObject
     */
    private function mockIndexAllMessages(): mixed
    {
        $indexAllMessages = $this->createMock(
            IndexAllMessages::class,
        );

        $indexAllMessages->method('index')->willReturnCallback(
            function (): void {
                $this->calls[] = [
                    'object' => 'IndexAllMessages',
                    'method' => 'index',
                ];
            }
        );

        return $indexAllMessages;
    }

    public function testSetUpIndices(): void
    {
        $this->api->setUpIndices();

        self::assertSame(
            [
                [
                    'object' => 'SetUpIndices',
                    'method' => 'setUp',
                ],
            ],
            $this->calls,
        );
    }

    public function testIndexMessage(): void
    {
        $message = $this->createMock(Entry::class);

        $this->api->indexMessage(message: $message);

        self::assertSame(
            [
                [
                    'object' => 'IndexMessage',
                    'method' => 'index',
                    'message' => $message,
                ],
            ],
            $this->calls,
        );
    }

    public function testIndexAllMessages(): void
    {
        $this->api->indexAllMessages();

        self::assertSame(
            [
                [
                    'object' => 'IndexAllMessages',
                    'method' => 'index',
                ],
            ],
            $this->calls,
        );
    }

    public function testQueueIndexAllMessages(): void
    {
        $this->api->queueIndexAllMessages();

        self::assertCount(1, $this->calls);

        self::assertSame(
            'Queue',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'push',
            $this->calls[0]['method'],
        );

        $job = $this->calls[0]['job'];

        self::assertInstanceOf(
            IndexAllMessagesQueueJob::class,
            $job,
        );
    }
}
