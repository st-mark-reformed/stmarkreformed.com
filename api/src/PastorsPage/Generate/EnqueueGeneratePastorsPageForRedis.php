<?php

declare(strict_types=1);

namespace App\PastorsPage\Generate;

use BuzzingPixel\Queue\QueueHandler;

use function array_filter;
use function count;

readonly class EnqueueGeneratePastorsPageForRedis
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
                ) => $q['handle'] === GeneratePastorsPageForRedis::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            handle: GeneratePastorsPageForRedis::JOB_HANDLE,
            name: GeneratePastorsPageForRedis::JOB_NAME,
            class: GeneratePastorsPageForRedis::class,
            method: 'generate',
        );
    }
}
