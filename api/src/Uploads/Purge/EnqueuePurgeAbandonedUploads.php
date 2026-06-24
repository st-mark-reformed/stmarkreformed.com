<?php

declare(strict_types=1);

namespace App\Uploads\Purge;

use BuzzingPixel\Queue\QueueHandler;

readonly class EnqueuePurgeAbandonedUploads
{
    public function __construct(private QueueHandler $queueHandler)
    {
    }

    public function enqueue(): void
    {
        foreach ($this->queueHandler->getEnqueuedItems()->queueItems as $item) {
            if ($item->handle === PurgeAbandonedUploads::JOB_HANDLE) {
                return;
            }
        }

        $this->queueHandler->enqueueJob(
            handle: PurgeAbandonedUploads::JOB_HANDLE,
            name: PurgeAbandonedUploads::JOB_NAME,
            class: PurgeAbandonedUploads::class,
            method: 'purge',
        );
    }
}
