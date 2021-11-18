<?php

declare(strict_types=1);

namespace App\ElasticSearch\SetUpIndices;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SetUpIndicesTest extends TestCase
{
    private SetUpIndices $setUpIndices;

    /** @var mixed[] */
    private array $calls = [];

    private bool $getIndexThrowsException = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->getIndexThrowsException = false;

        $this->setUpIndices = new SetUpIndices(
            client: $this->mockClient(),
        );
    }

    /**
     * @return MockObject&Client
     */
    private function mockClient(): mixed
    {
        $client = $this->createMock(Client::class);

        $client->method('indices')->willReturn(
            $this->mockIndicesNamespace(),
        );

        return $client;
    }

    /**
     * @return MockObject&IndicesNamespace
     */
    private function mockIndicesNamespace(): mixed
    {
        $namespace = $this->createMock(
            IndicesNamespace::class
        );

        $namespace->method('get')->willReturnCallback(
            function (array $params): array {
                $this->calls[] = [
                    'object' => 'IndicesNamespace',
                    'method' => 'get',
                    'params' => $params,
                ];

                if ($this->getIndexThrowsException) {
                    throw new Exception('foo bar');
                }

                return ['foo bar baz'];
            }
        );

        $namespace->method('create')->willReturnCallback(
            function (array $params): array {
                $this->calls[] = [
                    'object' => 'IndicesNamespace',
                    'method' => 'create',
                    'params' => $params,
                ];

                return ['foo bar baz'];
            }
        );

        return $namespace;
    }

    public function testSetUpWhenExists(): void
    {
        $this->getIndexThrowsException = false;

        $this->setUpIndices->setUp();

        self::assertSame(
            [
                [
                    'object' => 'IndicesNamespace',
                    'method' => 'get',
                    'params' => ['index' => 'messages'],
                ],
            ],
            $this->calls,
        );
    }

    public function testSetUpWhenDoesNotExists(): void
    {
        $this->getIndexThrowsException = true;

        $this->setUpIndices->setUp();

        self::assertSame(
            [
                [
                    'object' => 'IndicesNamespace',
                    'method' => 'get',
                    'params' => ['index' => 'messages'],
                ],
                [
                    'object' => 'IndicesNamespace',
                    'method' => 'create',
                    'params' => ['index' => 'messages'],
                ],
            ],
            $this->calls,
        );
    }
}
