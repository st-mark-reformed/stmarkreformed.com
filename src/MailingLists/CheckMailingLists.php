<?php


declare(strict_types=1);

namespace App\MailingLists;

use BuzzingPixel\CraftScheduler\Frequency;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItemCollection;

class CheckMailingLists
{
    public static function addSchedule(
        ScheduleConfigItemCollection $schedule,
    ): void {
        $schedule->addItem(new ScheduleConfigItem(
            className: self::class,
            runEvery: Frequency::ALWAYS,
        ));
    }

    public function __invoke(): void
    {
    }
}
