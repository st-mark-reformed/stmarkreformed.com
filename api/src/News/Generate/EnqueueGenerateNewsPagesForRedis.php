<?php

declare(strict_types=1);

namespace App\News\Generate;

use BuzzingPixel\Queue\QueueHandler;

use function array_filter;
use function count;

readonly class EnqueueGenerateNewsPagesForRedis
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
                ) => $q['handle'] === GenerateNewsPagesForRedis::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            handle: GenerateNewsPagesForRedis::JOB_HANDLE,
            name: GenerateNewsPagesForRedis::JOB_NAME,
            class: GenerateNewsPagesForRedis::class,
            method: 'generate',
        );
    }
}
