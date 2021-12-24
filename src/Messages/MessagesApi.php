<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Queue\SetMessageSeriesLatestEntryQueueJob;
use App\Messages\RetrieveMessages\MessageRetrieval;
use App\Messages\RetrieveMessages\MessageRetrievalParams;
use App\Messages\RetrieveMessages\MessagesResult;
use App\Messages\SetMessageSeriesLatestEntryDate\SetMessageSeriesLatestEntry;
use craft\queue\Queue;

class MessagesApi
{
    public function __construct(
        private Queue $queue,
        private MessageRetrieval $messageRetrieval,
        private SetMessageSeriesLatestEntry $setMessageSeriesLatestEntry,
    ) {
    }

    public function retrieveMessages(
        ?MessageRetrievalParams $params = null
    ): MessagesResult {
        return $this->messageRetrieval->fromParams(params: $params);
    }

    public function setMessageSeriesLatestEntry(): void
    {
        $this->setMessageSeriesLatestEntry->set();
    }

    public function queueSetMessageSeriesLatestEntry(): void
    {
        $this->queue->push(new SetMessageSeriesLatestEntryQueueJob());
    }
}
