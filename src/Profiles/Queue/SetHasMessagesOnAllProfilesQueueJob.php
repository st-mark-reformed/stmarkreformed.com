<?php

declare(strict_types=1);

namespace App\Profiles\Queue;

use App\Profiles\SetHasMessages\SetHasMessagesOnAllProfiles;
use Config\di\Container;
use craft\queue\BaseJob;

use function assert;

/**
 * @codeCoverageIgnore
 */
class SetHasMessagesOnAllProfilesQueueJob extends BaseJob
{
    protected function defaultDescription(): string
    {
        return 'Set has messages on all profiles';
    }

    /**
     * @inheritDoc
     */
    public function execute($queue): void
    {
        $set = Container::get()->get(SetHasMessagesOnAllProfiles::class);

        assert($set instanceof SetHasMessagesOnAllProfiles);

        $set->set();
    }
}
