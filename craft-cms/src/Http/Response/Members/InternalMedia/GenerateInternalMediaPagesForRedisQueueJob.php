<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia;

use Config\di\Container;
use craft\queue\BaseJob;

class GenerateInternalMediaPagesForRedisQueueJob extends BaseJob
{
    const DESCRIPTION = 'Generate internal media pages for redis';

    protected function defaultDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function execute($queue)
    {
        $generator = Container::get()->get(
            GenerateInternalMediaPagesForRedis::class,
        );

        assert($generator instanceof GenerateInternalMediaPagesForRedis);

        $generator->generate();
    }
}
