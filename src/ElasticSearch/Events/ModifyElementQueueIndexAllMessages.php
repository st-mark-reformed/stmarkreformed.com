<?php

declare(strict_types=1);

namespace App\ElasticSearch\Events;

use App\ElasticSearch\ElasticSearchApi;
use craft\elements\Entry;
use yii\base\Event;
use yii\base\InvalidConfigException;

class ModifyElementQueueIndexAllMessages
{
    public function __construct(private ElasticSearchApi $elasticSearchApi)
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

        $this->elasticSearchApi->queueIndexAllMessages();
    }
}
