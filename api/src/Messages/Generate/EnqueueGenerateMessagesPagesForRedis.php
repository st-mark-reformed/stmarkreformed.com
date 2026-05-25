<?php

declare(strict_types=1);

namespace App\Messages\Generate;

use BuzzingPixel\Queue\QueueHandler;

use function array_filter;
use function count;

readonly class EnqueueGenerateMessagesPagesForRedis
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
                ) => $q['handle'] === GenerateMessagesPagesForRedis::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            handle: GenerateMessagesPagesForRedis::JOB_HANDLE,
            name: GenerateMessagesPagesForRedis::JOB_NAME,
            class: GenerateMessagesPagesForRedis::class,
            method: 'generate',
        );
    }
}
