<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\Single;

use craft\elements\Entry;
use Elasticsearch\Client;
use Throwable;

class IndexMessageEntryFactory
{
    public function __construct(
        private Client $client,
        private CreateMessageIndex $create,
        private UpdateMessageIndex $update,
    ) {
    }

    public function make(Entry $message): IndexMessageEntryContract
    {
        try {
            $this->client->get([
                'index' => 'messages',
                'id' => $message->uid,
            ]);

            return $this->update;
        } catch (Throwable) {
            return $this->create;
        }
    }
}
