<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\Single;

use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use Elasticsearch\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class CreateMessageIndexTest extends TestCase
{
    private CreateMessageIndex $createMessageIndex;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->createMessageIndex = new CreateMessageIndex(
            client: $this->mockClient(),
            createMessageIndexBody: $this->mockCreateMessageIndexBody(),
        );
    }

    /**
     * @return MockObject&Client
     */
    private function mockClient(): mixed
    {
        $client = $this->createMock(Client::class);

        $client->method('index')->willReturnCallback(
            function (array $params): array {
                $this->calls[] = [
                    'object' => 'Client',
                    'method' => 'index',
                    'params' => $params,
                ];

                return ['fooBar'];
            }
        );

        return $client;
    }

    /**
     * @return MockObject&CreateMessageIndexBody
     */
    private function mockCreateMessageIndexBody(): mixed
    {
        $createMessageIndexBody = $this->createMock(
            CreateMessageIndexBody::class,
        );

        $createMessageIndexBody->method('fromMessage')
            ->willReturnCallback(
                function (Entry $message): array {
                    $this->calls[] = [
                        'object' => 'CreateMessageIndexBody',
                        'method' => 'fromMessage',
                        'message' => $message,
                    ];

                    return ['fooBarBody'];
                }
            );

        return $createMessageIndexBody;
    }

    /**
     * @throws InvalidFieldException
     */
    public function testIndex(): void
    {
        $message = $this->createMock(Entry::class);

        $message->uid = 'fooBarUid';

        $this->createMessageIndex->index(message: $message);

        self::assertCount(2, $this->calls);

        self::assertSame(
            'CreateMessageIndexBody',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'fromMessage',
            $this->calls[0]['method'],
        );

        $messageFromCall = $this->calls[0]['message'];

        self::assertSame(
            $message,
            $messageFromCall,
        );

        self::assertSame(
            [
                'object' => 'Client',
                'method' => 'index',
                'params' => [
                    'index' => 'messages',
                    'id' => 'fooBarUid',
                    'body' => ['fooBarBody'],
                ],
            ],
            $this->calls[1],
        );
    }
}
