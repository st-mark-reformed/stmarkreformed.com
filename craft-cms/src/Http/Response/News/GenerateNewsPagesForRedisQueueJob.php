<?php

declare(strict_types=1);

namespace App\Http\Response\News;

use Config\di\Container;
use craft\queue\BaseJob;

class GenerateNewsPagesForRedisQueueJob extends BaseJob
{
    const DESCRIPTION = 'Generate News pages for redis';

    protected function defaultDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function execute($queue)
    {
        $generator = Container::get()->get(GenerateNewsPagesForRedis::class);

        assert($generator instanceof GenerateNewsPagesForRedis);

        $generator->generate();
    }
}
