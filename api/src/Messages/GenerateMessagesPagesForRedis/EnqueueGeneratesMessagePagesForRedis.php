<?php

declare(strict_types=1);

namespace App\Messages\GenerateMessagesPagesForRedis;

use BuzzingPixel\Queue\QueueHandler;

use function array_filter;
use function count;

readonly class EnqueueGeneratesMessagePagesForRedis
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
                    array $q,
                ) => $q['handle'] === GenerateMessagesPagesForRedis::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            GenerateMessagesPagesForRedis::JOB_HANDLE,
            GenerateMessagesPagesForRedis::JOB_NAME,
            GenerateMessagesPagesForRedis::class,
        );
    }
}
