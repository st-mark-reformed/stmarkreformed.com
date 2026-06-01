<?php

declare(strict_types=1);

namespace App\InternalMessages\Generate;

use BuzzingPixel\Queue\QueueHandler;

use function array_filter;
use function count;

readonly class EnqueueGenerateInternalMediaPagesForRedis
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
                ) => $q['handle'] === GenerateInternalMediaPagesForRedis::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            handle: GenerateInternalMediaPagesForRedis::JOB_HANDLE,
            name: GenerateInternalMediaPagesForRedis::JOB_NAME,
            class: GenerateInternalMediaPagesForRedis::class,
            method: 'generate',
        );
    }
}
