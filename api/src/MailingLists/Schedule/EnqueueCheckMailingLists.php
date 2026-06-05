<?php

declare(strict_types=1);

namespace App\MailingLists\Schedule;

use BuzzingPixel\Queue\QueueHandler;

use function array_filter;
use function count;

readonly class EnqueueCheckMailingLists
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
                ) => $q['handle'] === CheckMailingListsJob::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            handle: CheckMailingListsJob::JOB_HANDLE,
            name: CheckMailingListsJob::JOB_NAME,
            class: CheckMailingListsJob::class,
            method: 'check',
        );
    }
}
