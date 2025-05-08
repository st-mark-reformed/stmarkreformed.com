<?php

declare(strict_types=1);

namespace App\Profiles\Events;

use App\Profiles\ProfilesApi;
use craft\elements\Entry;
use yii\base\Event;
use yii\base\InvalidConfigException;

class ModifyElementQueueSetHasMessagesOnAllProfiles
{
    public function __construct(private ProfilesApi $profilesApi)
    {
    }

    /**
     * @throws InvalidConfigException
     */
    public function respond(Event $event): void
    {
        $element = $event->sender;

        if (! ($element instanceof Entry)) {
            return;
        }

        if ($element->getType()->handle !== 'messages') {
            return;
        }

        $this->profilesApi->queueSetHasMessagesOnAllProfiles();
    }
}
