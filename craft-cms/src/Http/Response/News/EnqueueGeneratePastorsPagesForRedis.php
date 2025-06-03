<?php

declare(strict_types=1);

namespace App\Http\Response\News;

use BuzzingPixel\CraftScheduler\Frequency;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItemCollection;
use craft\queue\Queue;

class EnqueueGeneratePastorsPagesForRedis
{
    public static function addSchedule(
        ScheduleConfigItemCollection $schedule,
    ): void {
        $schedule->addItem(new ScheduleConfigItem(
            className: self::class,
            runEvery: Frequency::FIVE_MINUTES,
        ));
    }

    public function __construct(private Queue $queue)
    {
    }

    public function __invoke(): void
    {
        $this->enqueue();
    }

    public function enqueue(): void
    {
        $queueItems = $this->queue->getJobInfo();

        foreach ($queueItems as $queueItem) {
            $desc = $queueItem['description'] ?? '';

            if ($desc !== GeneratePastorsPagesForRedisQueueJob::DESCRIPTION) {
                continue;
            }

            return;
        }

        $this->queue->push(new GeneratePastorsPagesForRedisQueueJob());
    }
}
