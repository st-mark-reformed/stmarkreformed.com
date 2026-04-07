<?php

declare(strict_types=1);

namespace App\Messages\Search;

use App\Messages\Message;
use App\Messages\MessagesRepository;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;

use function array_map;
use function assert;

readonly class IndexAllMessages
{
    public const string JOB_HANDLE = 'index-all-messages';

    public const string JOB_NAME = 'Index All Messages';

    public function __construct(
        private Client $client,
        private IndexMessage $indexMessage,
        private SetUpIndices $setUpIndices,
        private MessagesRepository $repository,
        private DeleteMessagesFromIndexIfNotPresentInDb $deleteMessagesFromIndexIfNotPresentInDb,
    ) {
    }

    public function index(): void
    {
        // Ensure indices are set up
        $this->setUpIndices->setUp();

        $messages = $this->repository->findAll();

        $messageIds = $messages->map(
            static function (Message $message): string {
                return $message->id->toString();
            },
        );

        $index = $this->client->search([
            'index' => MessagesSearchIndex::MESSAGES->value,
            'body' => ['size' => 10000],
        ]);

        assert($index instanceof Elasticsearch);

        $indexedIds = array_map(
            /**
             * @param array{
             *     _index: string,
             *     _type: string,
             *     _id: string,
             *     _score: float,
             *     _source: array{
             *         title: string,
             *         slug: string,
             *         speakerName: string,
             *         speakerId: string,
             *         passage: string,
             *         messageSeries: string,
             *         messageSeriesSlug: string,
             *         messageSeriesId: string,
             *         shortDescription: string,
             *     }
             * } $item
             *
             * @phpstan-ignore-next-line
             */
            static function (array $item): string {
                /** @phpstan-ignore-next-line */
                return $item['_id'];
            },
            /** @phpstan-ignore-next-line */
            $index['hits']['hits'],
        );

        $this->deleteMessagesFromIndexIfNotPresentInDb->delete(
            messageIds: $messageIds,
            indexedIds: $indexedIds,
        );

        $messages->map(function (Message $message): void {
            $this->indexMessage->index(message: $message);
        });
    }
}
