<?php

declare(strict_types=1);

namespace App\Profiles;

use App\Profiles\Queue\SetHasMessagesOnAllProfilesQueueJob;
use App\Profiles\SetHasMessages\SetHasMessagesOnAllProfiles;
use craft\queue\Queue;

class ProfilesApi
{
    public function __construct(
        private Queue $queue,
        private SetHasMessagesOnAllProfiles $setHasMessagesOnAllProfiles,
    ) {
    }

    public function setHasMessagesOnAllProfiles(): void
    {
        $this->setHasMessagesOnAllProfiles->set();
    }

    public function queueSetHasMessagesOnAllProfiles(): void
    {
        $this->queue->push(new SetHasMessagesOnAllProfilesQueueJob());
    }
}
