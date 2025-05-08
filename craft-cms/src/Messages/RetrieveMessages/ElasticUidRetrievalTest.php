<?php

declare(strict_types=1);

namespace App\Messages\RetrieveMessages;

use Elasticsearch\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ElasticUidRetrievalTest extends TestCase
{
    private ElasticUidRetrieval $elasticUidRetrieval;

    /** @var mixed[] */
    private array $calls = [];

    private bool $clientReturnsHits = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->clientReturnsHits = false;

        $this->elasticUidRetrieval = new ElasticUidRetrieval(
            client: $this->mockClient(),
        );
    }

    /**
     * @return Client&MockObject
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

                if (! $this->clientReturnsHits) {
                    return [];
                }

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

    public function testWhenHasNoParams(): void
    {
        $params = new MessageRetrievalParams();

        self::assertSame(
            [],
            $this->elasticUidRetrieval->fromParams(params: $params),
        );

        self::assertSame([], $this->calls);
    }

    public function testWhenNoHits(): void
    {
        $params = new MessageRetrievalParams(
            by: ['by1', 'by2'],
            series: ['series1', 'series2'],
            scriptureReference: 'scripture1',
            title: 'title1',
        );

        self::assertSame(
            [],
            $this->elasticUidRetrieval->fromParams(params: $params),
        );

        self::assertSame(
            [
                [
                    'object' => 'Client',
                    'method' => 'search',
                    'params' => [
                        'index' => 'messages',
                        'body' => [
                            'size' => 10000,
                            'query' => [
                                'bool' => [
                                    'should' => [
                                        [
                                            'simple_query_string' => [
                                                'fields' => ['speakerSlug'],
                                                'query' => 'by1',
                                            ],
                                        ],
                                        [
                                            'simple_query_string' => [
                                                'fields' => ['speakerSlug'],
                                                'query' => 'by2',
                                            ],
                                        ],
                                        [
                                            'simple_query_string' => [
                                                'fields' => ['messageSeriesSlug'],
                                                'query' => 'series1',
                                            ],
                                        ],
                                        [
                                            'simple_query_string' => [
                                                'fields' => ['messageSeriesSlug'],
                                                'query' => 'series2',
                                            ],
                                        ],
                                        [
                                            'simple_query_string' => [
                                                'fields' => ['messageText'],
                                                'query' => 'scripture1',
                                            ],
                                        ],
                                        [
                                            'simple_query_string' => [
                                                'fields' => ['title'],
                                                'query' => 'title1',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $this->calls,
        );
    }

    public function testWithHits(): void
    {
        $this->clientReturnsHits = true;

        $params = new MessageRetrievalParams(
            title: 'title1',
        );

        self::assertSame(
            [
                'fooId1',
                'fooId2',
            ],
            $this->elasticUidRetrieval->fromParams(params: $params),
        );

        self::assertSame(
            [
                [
                    'object' => 'Client',
                    'method' => 'search',
                    'params' => [
                        'index' => 'messages',
                        'body' => [
                            'size' => 10000,
                            'query' => [
                                'bool' => [
                                    'should' => [
                                        [
                                            'simple_query_string' => [
                                                'fields' => ['title'],
                                                'query' => 'title1',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
