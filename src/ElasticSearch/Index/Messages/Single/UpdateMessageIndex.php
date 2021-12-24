<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\Single;

use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use Elasticsearch\Client;

class UpdateMessageIndex implements IndexMessageEntryContract
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
        $this->client->update([
            'index' => 'messages',
            'id' => $message->uid,
            'body' => [
                'doc' => $this->createMessageIndexBody->fromMessage(
                    message: $message,
                ),
            ],
        ]);
    }
}
