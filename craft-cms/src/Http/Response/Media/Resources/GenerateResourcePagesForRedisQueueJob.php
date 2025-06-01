<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources;

use Config\di\Container;
use craft\queue\BaseJob;

class GenerateResourcePagesForRedisQueueJob extends BaseJob
{
    const DESCRIPTION = 'Generate Resource pages for redis';

    protected function defaultDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function execute($queue)
    {
        $generator = Container::get()->get(
            GenerateResourcePagesForRedis::class,
        );

        assert($generator instanceof GenerateResourcePagesForRedis);

        $generator->generate();
    }
}
