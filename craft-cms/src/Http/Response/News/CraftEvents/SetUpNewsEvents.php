<?php

declare(strict_types=1);

namespace App\Http\Response\News\CraftEvents;

use App\Http\Response\News\EnqueueGenerateNewsPagesForRedis;
use craft\base\Element;
use yii\base\Event;

class SetUpNewsEvents
{
    public function __construct(
        private EnqueueGenerateNewsPagesForRedis $enqueue,
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
