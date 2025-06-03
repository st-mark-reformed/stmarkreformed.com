<?php

declare(strict_types=1);

namespace App\Http\Response\News;

use Config\di\Container;
use craft\queue\BaseJob;

class GeneratePastorsPagesForRedisQueueJob extends BaseJob
{
    const DESCRIPTION = 'Generate Pastors Page pages for redis';

    protected function defaultDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function execute($queue)
    {
        $generator = Container::get()->get(GeneratePastorsPagesForRedis::class);

        assert($generator instanceof GeneratePastorsPagesForRedis);

        $generator->generate();
    }
}
