<?php

declare(strict_types=1);

namespace App\Messages\Events;

use App\Messages\MessagesApi;
use craft\elements\Entry;
use yii\base\Event;
use yii\base\InvalidConfigException;

class ModifyElementQueueSetMessageSeriesLatestEntry
{
    public function __construct(private MessagesApi $messagesApi)
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

        $this->messagesApi->queueSetMessageSeriesLatestEntry();
    }
}
