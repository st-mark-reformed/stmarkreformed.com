<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth;

use Config\di\Container;
use craft\queue\BaseJob;

class GenerateHymnsOfTheMonthPageForRedisQueueJob extends BaseJob
{
    const DESCRIPTION = 'Generate internal hymn of the month pages for redis';

    protected function defaultDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function execute($queue)
    {
        $generator = Container::get()->get(
            GenerateHymnsOfTheMonthPageForRedis::class,
        );

        assert($generator instanceof GenerateHymnsOfTheMonthPageForRedis);

        $generator->generate();
    }
}
