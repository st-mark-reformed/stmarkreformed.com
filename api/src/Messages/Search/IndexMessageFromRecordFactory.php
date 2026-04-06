<?php

declare(strict_types=1);

namespace App\Messages\Search;

use App\Messages\Message;
use Elastic\Elasticsearch\Client;
use Throwable;

readonly class IndexMessageFromRecordFactory
{
    public function __construct(
        private Client $client,
        private IndexMessageFromRecordCreate $create,
        private IndexMessageFromRecordUpdate $update,
    ) {
    }

    public function make(Message $message): IndexMessageFromRecord
    {
        try {
            $this->client->get([
                'index' => MessagesSearchIndex::MESSAGES->value,
                'id' => $message->id->toString(),
            ]);

            return $this->update;
        } catch (Throwable) {
            return $this->create;
        }
    }
}
