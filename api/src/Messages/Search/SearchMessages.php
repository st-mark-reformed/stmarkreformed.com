<?php

declare(strict_types=1);

namespace App\Messages\Search;

use App\Messages\Message;
use App\Messages\Messages;
use App\Messages\MessagesRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;

use function array_flip;
use function array_map;
use function assert;
use function count;

readonly class SearchMessages
{
    private const int ELASTIC_PAGE_SIZE = 10000;

    public function __construct(
        private Client $client,
        private MessagesRepository $repository,
    ) {
    }

    public function find(SearchMessagesParams $params): Messages
    {
        $messages = $this->repository->findAll()->filter(
            callback: static fn (Message $message): bool => $message->isEnabled,
        );

        $messages = $this->filterByElasticIds(
            messages: $messages,
            params: $params,
        );

        return $this->filterByDateRange(
            messages: $messages,
            params: $params,
        );
    }

    private function filterByElasticIds(
        Messages $messages,
        SearchMessagesParams $params,
    ): Messages {
        if (! $params->hasTextSearch()) {
            return $messages;
        }

        $matchingIds = $this->queryElasticForIds(params: $params);

        if (count($matchingIds) === 0) {
            return new Messages(items: []);
        }

        $idSet = array_flip($matchingIds);

        return $messages->filter(
            callback: static fn (Message $message): bool => isset(
                $idSet[$message->id->toString()],
            ),
        );
    }

    /** @return string[] */
    private function queryElasticForIds(SearchMessagesParams $params): array
    {
        $shouldClauses = [];

        foreach ($params->bySpeakerSlugs as $slug) {
            $shouldClauses[] = [
                'simple_query_string' => [
                    'fields' => ['speakerSlug'],
                    'query' => '"' . $slug . '"',
                ],
            ];
        }

        foreach ($params->bySeriesSlugs as $slug) {
            $shouldClauses[] = [
                'simple_query_string' => [
                    'fields' => ['messageSeriesSlug'],
                    'query' => '"' . $slug . '"',
                ],
            ];
        }

        if ($params->scriptureReference !== '') {
            $shouldClauses[] = [
                'simple_query_string' => [
                    'fields' => ['passage'],
                    'query' => $params->scriptureReference,
                ],
            ];
        }

        if ($params->title !== '') {
            $shouldClauses[] = [
                'simple_query_string' => [
                    'fields' => ['title'],
                    'query' => $params->title,
                ],
            ];
        }

        $response = $this->client->search([
            'index' => MessagesSearchIndex::MESSAGES->value,
            'body' => [
                'size' => self::ELASTIC_PAGE_SIZE,
                'query' => [
                    'bool' => ['should' => $shouldClauses],
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

    private function filterByDateRange(
        Messages $messages,
        SearchMessagesParams $params,
    ): Messages {
        if (! $params->hasDateRange()) {
            return $messages;
        }

        $start = $params->dateRangeStartAsDate();
        $end   = $params->dateRangeEndAsDate();

        return $messages->filter(
            callback: static function (Message $message) use ($start, $end): bool {
                return self::dateInRange(
                    messageDate: $message->date,
                    start: $start,
                    end: $end,
                );
            },
        );
    }

    private static function dateInRange(
        DateTimeInterface $messageDate,
        DateTimeImmutable|null $start,
        DateTimeImmutable|null $end,
    ): bool {
        if ($start !== null && $messageDate < $start) {
            return false;
        }

        return $end === null || $messageDate <= $end;
    }
}
