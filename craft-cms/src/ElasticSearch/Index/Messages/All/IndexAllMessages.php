<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\All;

use App\ElasticSearch\Index\Messages\Single\IndexMessage;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Entry;
use Elasticsearch\Client;

use function array_map;

class IndexAllMessages
{
    public function __construct(
        private Client $client,
        private IndexMessage $indexMessage,
        private EntryQueryFactory $entryQueryFactory,
        private DeleteEntriesNotPresent $deleteEntriesNotPresent,
    ) {
    }

    public function index(): void
    {
        $entryQuery = $this->entryQueryFactory->make();

        $entryQuery->section('messages');

        $entryQuery->limit(999999);

        /** @var Entry[] $entries */
        $entries = $entryQuery->all();

        $entryIds = array_map(
            static fn (Entry $e) => (string) $e->uid,
            $entries,
        );

        $index = $this->client->search([
            'index' => 'messages',
            'body' => ['size' => 10000],
        ]);

        $indexedIds = array_map(
            static fn (array $i) => $i['_id'],
            $index['hits']['hits'],
        );

        $this->deleteEntriesNotPresent->run(
            entryIds: $entryIds,
            indexedIds: $indexedIds,
        );

        array_map(
            [$this->indexMessage, 'index'],
            $entries,
        );
    }
}
