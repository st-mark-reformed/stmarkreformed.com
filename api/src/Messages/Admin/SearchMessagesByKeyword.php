<?php

declare(strict_types=1);

namespace App\Messages\Admin;

use App\Messages\Message;
use App\Messages\Messages;
use App\Messages\MessagesRepository;
use App\Messages\Search\MessagesSearchIndex;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;

use function array_flip;
use function array_map;
use function assert;
use function count;

readonly class SearchMessagesByKeyword
{
    private const int ELASTIC_PAGE_SIZE = 10000;

    private const array SEARCH_FIELDS = [
        'title',
        'passage',
        'speakerName',
        'messageSeries',
        'slug',
    ];

    public function __construct(
        private Client $client,
        private MessagesRepository $repository,
    ) {
    }

    public function find(string $keyword): Messages
    {
        $matchingIds = $this->queryElasticForIds(keyword: $keyword);

        if (count($matchingIds) === 0) {
            return new Messages(items: []);
        }

        $idSet = array_flip($matchingIds);

        return $this->repository->findAll()->filter(
            callback: static fn (Message $message): bool => isset(
                $idSet[$message->id->toString()],
            ),
        );
    }

    /** @return string[] */
    private function queryElasticForIds(string $keyword): array
    {
        $response = $this->client->search([
            'index' => MessagesSearchIndex::MESSAGES->value,
            'body' => [
                'size' => self::ELASTIC_PAGE_SIZE,
                'query' => [
                    'simple_query_string' => [
                        'fields' => self::SEARCH_FIELDS,
                        'query' => $keyword,
                        'default_operator' => 'and',
                    ],
                ],
            ],
        ]);

        assert($response instanceof Elasticsearch);

        /** @phpstan-ignore-next-line */
        return array_map(
            /** @phpstan-ignore-next-line */
            static fn (array $hit): string => (string) $hit['_id'],
            /** @phpstan-ignore-next-line */
            $response['hits']['hits'] ?? [],
        );
    }
}
