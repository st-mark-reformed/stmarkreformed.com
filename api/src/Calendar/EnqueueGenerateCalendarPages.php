<?php

declare(strict_types=1);

namespace App\Calendar;

use BuzzingPixel\Queue\QueueHandler;
use BuzzingPixel\Queue\QueueItemWithKey;

use function array_filter;
use function count;

readonly class EnqueueGenerateCalendarPages
{
    public function __construct(private QueueHandler $queueHandler)
    {
    }

    public function __invoke(): void
    {
        $this->enqueue();
    }

    public function enqueue(): void
    {
        if (
            count(array_filter(
                $this->queueHandler->getEnqueuedItems()->asArray(),
                static fn (
                    QueueItemWithKey $q,
                ) => $q->handle === GenerateCalendarPages::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            GenerateCalendarPages::JOB_HANDLE,
            GenerateCalendarPages::JOB_NAME,
            GenerateCalendarPages::class,
        );
    }
}
