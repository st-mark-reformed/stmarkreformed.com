<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\All;

use Elasticsearch\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeleteEntriesNotPresentTest extends TestCase
{
    private DeleteEntriesNotPresent $deleteEntriesNotPresent;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->deleteEntriesNotPresent = new DeleteEntriesNotPresent(
            client: $this->mockClient(),
        );
    }

    /**
     * @return MockObject&Client
     */
    private function mockClient(): mixed
    {
        $client = $this->createMock(Client::class);

        $client->method('delete')->willReturnCallback(
            function (array $params): array {
                $this->calls[] = [
                    'object' => 'Client',
                    'method' => 'delete',
                    'params' => $params,
                ];

                return ['fooBar'];
            }
        );

        return $client;
    }

    public function testRun(): void
    {
        $this->deleteEntriesNotPresent->run(
            ['id1', 'id3'],
            ['id1', 'id2', 'id3', 'id4'],
        );

        self::assertSame(
            [
                [
                    'object' => 'Client',
                    'method' => 'delete',
                    'params' => [
                        'index' => 'messages',
                        'id' => 'id2',
                    ],
                ],
                [
                    'object' => 'Client',
                    'method' => 'delete',
                    'params' => [
                        'index' => 'messages',
                        'id' => 'id4',
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
