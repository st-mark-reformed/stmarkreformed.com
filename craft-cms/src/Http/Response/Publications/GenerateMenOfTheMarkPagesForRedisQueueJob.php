<?php

declare(strict_types=1);

namespace App\Http\Response\Publications;

use Config\di\Container;
use craft\queue\BaseJob;

class GenerateMenOfTheMarkPagesForRedisQueueJob extends BaseJob
{
    const DESCRIPTION = 'Generate Men of the Mark publication pages for redis';

    protected function defaultDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function execute($queue)
    {
        $generator = Container::get()->get(
            GenerateMenOfTheMarkPagesForRedis::class,
        );

        assert($generator instanceof GenerateMenOfTheMarkPagesForRedis);

        $generator->generate();
    }
}
