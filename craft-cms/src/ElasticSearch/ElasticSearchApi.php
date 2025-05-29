<?php

declare(strict_types=1);

namespace App\ElasticSearch;

use App\ElasticSearch\Index\Messages\All\IndexAllMessages;
use App\ElasticSearch\Index\Messages\Single\IndexMessage;
use App\ElasticSearch\Queue\IndexAllMessagesQueueJob;
use App\ElasticSearch\SetUpIndices\SetUpIndices;
use craft\elements\Entry;
use craft\queue\Queue;

class ElasticSearchApi
{
    public function __construct(
        private Queue $queue,
        private SetUpIndices $setUpIndices,
        private IndexMessage $indexMessage,
        private IndexAllMessages $indexAllMessages,
    ) {
    }

    public function setUpIndices(): void
    {
        $this->setUpIndices->setUp();
    }

    public function indexMessage(Entry $message): void
    {
        $this->indexMessage->index(message: $message);
    }

    public function indexAllMessages(): void
    {
        $this->indexAllMessages->index();
    }

    public function queueIndexAllMessages(): void
    {
        $queueItems = $this->queue->getJobInfo();

        foreach ($queueItems as $queueItem) {
            $desc = $queueItem['description'] ?? '';

            if ($desc !== IndexAllMessagesQueueJob::DESCRIPTION) {
                continue;
            }

            return;
        }

        $this->queue->push(new IndexAllMessagesQueueJob());
    }
}
