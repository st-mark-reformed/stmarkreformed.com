<?php

declare(strict_types=1);

namespace App\Messages\Search;

use BuzzingPixel\Queue\QueueHandler;

use function array_filter;
use function count;

readonly class EnqueueIndexAllMessages
{
    public function __construct(private QueueHandler $queueHandler)
    {
    }

    public function enqueue(): void
    {
        if (
            count(array_filter(
                $this->queueHandler->getEnqueuedItems()->asArray(),
                /** @phpstan-ignore-next-line */
                static fn (
                    array $q,
                ) => $q['handle'] === IndexAllMessages::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            handle: IndexAllMessages::JOB_HANDLE,
            name: IndexAllMessages::JOB_NAME,
            class: IndexAllMessages::class,
            method: 'index',
        );
    }
}
