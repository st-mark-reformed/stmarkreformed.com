<?php

declare(strict_types=1);

namespace App\Images\Queue;

use craft\queue\BaseJob;
use craft\queue\Queue;

use function array_filter;
use function count;

class PushToQueueIfNotInQueue
{
    public function __construct(private Queue $queue)
    {
    }

    public function push(BaseJob $job): void
    {
        $jobAlreadyInQueue = array_filter(
            $this->queue->getJobInfo(),
            static fn (
                array $q,
            ) => $q['description'] === $job->getDescription(),
        );

        if (count($jobAlreadyInQueue) > 0) {
            return;
        }

        $this->queue->push($job);
    }
}
