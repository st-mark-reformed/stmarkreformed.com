<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Generate;

use BuzzingPixel\Queue\QueueHandler;

use function array_filter;
use function count;

readonly class EnqueueGenerateHymnsOfTheMonthForRedis
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
                ) => $q['handle'] === GenerateHymnsOfTheMonthForRedis::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            handle: GenerateHymnsOfTheMonthForRedis::JOB_HANDLE,
            name: GenerateHymnsOfTheMonthForRedis::JOB_NAME,
            class: GenerateHymnsOfTheMonthForRedis::class,
            method: 'generate',
        );
    }
}
