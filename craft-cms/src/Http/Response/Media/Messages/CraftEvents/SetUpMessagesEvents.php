<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\CraftEvents;

use App\Http\Response\Media\Messages\EnqueueGenerateMessagesPagesForRedis;
use craft\base\Element;
use yii\base\Event;

class SetUpMessagesEvents
{
    public function __construct(
        private EnqueueGenerateMessagesPagesForRedis $enqueue,
    ) {
    }

    public function setUp(): void
    {
        $handler = [$this->enqueue, 'enqueue'];

        Event::on(
            Element::class,
            Element::EVENT_AFTER_SAVE,
            $handler
        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_DELETE,
            $handler
        );
    }
}
