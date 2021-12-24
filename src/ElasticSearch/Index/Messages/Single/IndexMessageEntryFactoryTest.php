<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\Single;

use craft\elements\Entry;
use Elasticsearch\Client;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IndexMessageEntryFactoryTest extends TestCase
{
    public IndexMessageEntryFactory $factory;

    /** @var mixed[] */
    private array $calls = [];

    private bool $clientThrowsException = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->clientThrowsException = false;

        $this->factory = new IndexMessageEntryFactory(
            client: $this->mockClient(),
            create: $this->createMock(
                CreateMessageIndex::class,
            ),
            update: $this->createMock(
                UpdateMessageIndex::class,
            ),
        );
    }

    /**
     * @return Client&MockObject
     */
    private function mockClient(): mixed
    {
        $client = $this->createMock(Client::class);

        $client->method('get')->willReturnCallback(
            function (array $params): array {
                $this->calls[] = [
                    'object' => 'Client',
                    'method' => 'get',
                    'params' => $params,
                ];

                if ($this->clientThrowsException) {
                    throw new Exception('foo');
                }

                return ['fooBar'];
            }
        );

        return $client;
    }

    public function testMakeWhenItemExists(): void
    {
        $message = $this->createMock(Entry::class);

        $message->uid = 'fooBarBazUid';

        self::assertInstanceOf(
            UpdateMessageIndex::class,
            $this->factory->make(message: $message),
        );

        self::assertSame(
            [
                [
                    'object' => 'Client',
                    'method' => 'get',
                    'params' => [
                        'index' => 'messages',
                        'id' => 'fooBarBazUid',
                    ],
                ],
            ],
            $this->calls,
        );
    }

    public function testMakeWhenItemDoesNotExists(): void
    {
        $this->clientThrowsException = true;

        $message = $this->createMock(Entry::class);

        $message->uid = 'fooBarUid';

        self::assertInstanceOf(
            CreateMessageIndex::class,
            $this->factory->make(message: $message),
        );

        self::assertSame(
            [
                [
                    'object' => 'Client',
                    'method' => 'get',
                    'params' => [
                        'index' => 'messages',
                        'id' => 'fooBarUid',
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
