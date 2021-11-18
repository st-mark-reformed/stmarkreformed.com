<?php

declare(strict_types=1);

namespace App\Messages\RetrieveMessages;

use Elasticsearch\Client;

use function array_map;
use function count;

class ElasticUidRetrieval
{
    public function __construct(private Client $client)
    {
    }

    /**
     * @return string[]
     */
    public function fromParams(MessageRetrievalParams $params): array
    {
        if ($params->hasNoSearch()) {
            return [];
        }

        $queries = [];

        if (count($params->by()) > 0) {
            foreach ($params->by() as $by) {
                $queries[] = [
                    'simple_query_string' => [
                        'fields' => ['speakerSlug'],
                        'query' => $by,
                    ],
                ];
            }
        }

        if (count($params->series()) > 0) {
            foreach ($params->series() as $series) {
                $queries[] = [
                    'simple_query_string' => [
                        'fields' => ['messageSeriesSlug'],
                        'query' => $series,
                    ],
                ];
            }
        }

        if ($params->scriptureReference() !== '') {
            $queries[] = [
                'simple_query_string' => [
                    'fields' => ['messageText'],
                    'query' => $params->scriptureReference(),
                ],
            ];
        }

        if ($params->title() !== '') {
            $queries[] = [
                'simple_query_string' => [
                    'fields' => ['title'],
                    'query' => $params->title(),
                ],
            ];
        }

        $results = $this->client->search([
            'index' => 'messages',
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => ['should' => $queries],
                ],
            ],
        ]);

        return array_map(
            static fn (array $i) => (string) $i['_id'],
            $results['hits']['hits'] ?? []
        );
    }
}
