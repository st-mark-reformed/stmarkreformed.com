<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages;

use Config\di\Container;
use craft\queue\BaseJob;

class GenerateMessagesPagesForRedisQueueJob extends BaseJob
{
    const DESCRIPTION = 'Generate messages pages for redis';

    protected function defaultDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function execute($queue)
    {
        $generator = Container::get()->get(
            GenerateMessagesPagesForRedis::class,
        );

        assert($generator instanceof GenerateMessagesPagesForRedis);

        $generator->generate();
    }
}
