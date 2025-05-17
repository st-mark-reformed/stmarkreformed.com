<?php

declare(strict_types=1);

namespace App\ElasticSearch\Queue;

use App\ElasticSearch\Index\Messages\All\IndexAllMessages;
use Config\di\Container;
use craft\queue\BaseJob;

use function assert;

/**
 * @codeCoverageIgnore
 */
class IndexAllMessagesQueueJob extends BaseJob
{
    const DESCRIPTION = 'Index all messages';

    protected function defaultDescription(): string
    {
        return self::DESCRIPTION;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue): void
    {
        $indexAllMessages = Container::get()->get(IndexAllMessages::class);

        assert($indexAllMessages instanceof IndexAllMessages);

        $indexAllMessages->index();
    }
}
