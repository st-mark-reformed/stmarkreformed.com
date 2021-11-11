<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\Single;

use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use Elasticsearch\Client;

class CreateMessageIndex implements IndexMessageEntryContract
{
    public function __construct(
        private Client $client,
        private CreateMessageIndexBody $createMessageIndexBody,
    ) {
    }

    /**
     * @throws InvalidFieldException
     */
    public function index(Entry $message): void
    {
        $this->client->index([
            'index' => 'messages',
            'id' => $message->uid,
            'body' => $this->createMessageIndexBody->fromMessage(
                message: $message,
            ),
        ]);
    }
}
