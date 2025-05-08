<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\All;

use App\ElasticSearch\Index\Messages\Single\IndexMessage;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use Elasticsearch\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IndexAllMessagesTest extends TestCase
{
    private IndexAllMessages $indexAllMessages;

    /** @var mixed[] */
    private array $calls = [];

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $entry1;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $entry2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->indexAllMessages = new IndexAllMessages(
            client: $this->mockClient(),
            indexMessage: $this->mockIndexMessage(),
            entryQueryFactory: $this->mockEntryQueryFactory(),
            deleteEntriesNotPresent: $this->mockDeleteEntriesNotPresent(),
        );
    }

    /**
     * @return MockObject&Client
     */
    private function mockClient(): mixed
    {
        $client = $this->createMock(Client::class);

        $client->method('search')->willReturnCallback(
            function (array $params): array {
                $this->calls[] = [
                    'object' => 'Client',
                    'method' => 'search',
                    'params' => $params,
                ];

                return [
                    'hits' => [
                        'hits' => [
                            ['_id' => 'fooId1'],
                            ['_id' => 'fooId2'],
                        ],
                    ],
                ];
            }
        );

        return $client;
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
     * @return MockObject&EntryQueryFactory
     */
    private function mockEntryQueryFactory(): mixed
    {
        $query = $this->createMock(EntryQuery::class);

        $query->method('section')->willReturnCallback(
            function (string $value) use (
                $query,
            ): EntryQuery {
                $this->calls[] = [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'value' => $value,
                ];

                return $query;
            }
        );

        $query->method('limit')->willReturnCallback(
            function (int $value) use (
                $query,
            ): EntryQuery {
                $this->calls[] = [
                    'object' => 'EntryQuery',
                    'method' => 'limit',
                    'value' => $value,
                ];

                return $query;
            }
        );

        $this->entry1 = $this->createMock(Entry::class);

        $this->entry1->uid = 'entry1Uid';

        $this->entry2 = $this->createMock(Entry::class);

        $this->entry2->uid = 'entry2Uid';

        $query->method('all')->willReturn([
            $this->entry1,
            $this->entry2,
        ]);

        $factory = $this->createMock(
            EntryQueryFactory::class,
        );

        $factory->method('make')->willReturn($query);

        return $factory;
    }

    /**
     * @return MockObject&DeleteEntriesNotPresent
     */
    private function mockDeleteEntriesNotPresent(): mixed
    {
        $deleteEntriesNotPresent = $this->createMock(
            DeleteEntriesNotPresent::class,
        );

        $deleteEntriesNotPresent->method('run')->willReturnCallback(
            function (array $entryIds, array $indexedIds): void {
                $this->calls[] = [
                    'object' => 'DeleteEntriesNotPresent',
                    'method' => 'run',
                    'entryIds' => $entryIds,
                    'indexedIds' => $indexedIds,
                ];
            }
        );

        return $deleteEntriesNotPresent;
    }

    public function testIndex(): void
    {
        $this->indexAllMessages->index();

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'value' => 'messages',
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'limit',
                    'value' => 999999,
                ],
                [
                    'object' => 'Client',
                    'method' => 'search',
                    'params' => [
                        'index' => 'messages',
                        'body' => ['size' => 10000],
                    ],
                ],
                [
                    'object' => 'DeleteEntriesNotPresent',
                    'method' => 'run',
                    'entryIds' => [
                        'entry1Uid',
                        'entry2Uid',
                    ],
                    'indexedIds' => [
                        'fooId1',
                        'fooId2',
                    ],
                ],
                [
                    'object' => 'IndexMessage',
                    'method' => 'index',
                    'message' => $this->entry1,
                ],
                [
                    'object' => 'IndexMessage',
                    'method' => 'index',
                    'message' => $this->entry2,
                ],
            ],
            $this->calls,
        );
    }
}
