<?php

declare(strict_types=1);

namespace App\Messages\Queue;

use App\Messages\SetMessageSeriesLatestEntryDate\SetMessageSeriesLatestEntry;
use Config\di\Container;
use craft\queue\BaseJob;

use function assert;

/**
 * @codeCoverageIgnore
 */
class SetMessageSeriesLatestEntryQueueJob extends BaseJob
{
    protected function defaultDescription(): string
    {
        return 'Set messages series latest entry';
    }

    /**
     * @inheritDoc
     */
    public function execute($queue): void
    {
        $set = Container::get()->get(SetMessageSeriesLatestEntry::class);

        assert($set instanceof SetMessageSeriesLatestEntry);

        $set->set();
    }
}
