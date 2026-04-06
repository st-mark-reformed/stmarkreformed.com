<?php

declare(strict_types=1);

namespace App\Messages\Search;

use App\Messages\Message;
use Elastic\Elasticsearch\Client;

readonly class IndexMessageFromRecordUpdate implements IndexMessageFromRecord
{
    public function __construct(private Client $client)
    {
    }

    public function index(Message $message): void
    {
        $this->client->update([
            'index' => MessagesSearchIndex::MESSAGES->value,
            'id' => $message->id->toString(),
            'body' => [
                'doc' => [
                    'title' => $message->title,
                    'slug' => $message->slug,
                    'speakerName' => $message->speaker->fullName,
                    'speakerId' => $message->speaker->id->toString(),
                    'passage' => $message->passage,
                    'messageSeries' => $message->series->title,
                    'messageSeriesSlug' => $message->series->slug->toString(),
                    'messageSeriesId' => $message->series->id->toString(),
                    'shortDescription' => $message->description,
                ],
            ],
        ]);
    }
}
