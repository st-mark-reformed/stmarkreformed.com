<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Generate;

use BuzzingPixel\Queue\QueueHandler;

use function array_filter;
use function count;

readonly class EnqueueGenerateMenOfTheMarkPagesForRedis
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
                ) => $q['handle'] === GenerateMenOfTheMarkPagesForRedis::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            handle: GenerateMenOfTheMarkPagesForRedis::JOB_HANDLE,
            name: GenerateMenOfTheMarkPagesForRedis::JOB_NAME,
            class: GenerateMenOfTheMarkPagesForRedis::class,
            method: 'generate',
        );
    }
}
