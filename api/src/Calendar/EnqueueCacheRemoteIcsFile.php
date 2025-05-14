<?php

declare(strict_types=1);

namespace App\Calendar;

use BuzzingPixel\Queue\QueueHandler;

use function array_filter;
use function count;

readonly class EnqueueCacheRemoteIcsFile
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
                ) => $q['handle'] === CacheRemoteIcsFile::JOB_HANDLE,
            )) > 0
        ) {
            return;
        }

        $this->queueHandler->enqueueJob(
            CacheRemoteIcsFile::JOB_HANDLE,
            CacheRemoteIcsFile::JOB_NAME,
            CacheRemoteIcsFile::class,
        );
    }
}
