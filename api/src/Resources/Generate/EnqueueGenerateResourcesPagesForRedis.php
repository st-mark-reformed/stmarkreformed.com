<?php

declare(strict_types=1);

namespace App\Resources\Generate;

use BuzzingPixel\Queue\QueueHandler;

use function array_filter;
use function count;

readonly class EnqueueGenerateResourcesPagesForRedis
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
                ) => $q['handle'] === GenerateResourcesPagesForRedis::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            handle: GenerateResourcesPagesForRedis::JOB_HANDLE,
            name: GenerateResourcesPagesForRedis::JOB_NAME,
            class: GenerateResourcesPagesForRedis::class,
            method: 'generate',
        );
    }
}
