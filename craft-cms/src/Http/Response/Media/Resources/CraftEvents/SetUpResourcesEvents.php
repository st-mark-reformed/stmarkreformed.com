<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources\CraftEvents;

use App\Http\Response\Media\Resources\EnqueueGenerateResourcePagesForRedis;
use craft\base\Element;
use yii\base\Event;

class SetUpResourcesEvents
{
    public function __construct(
        private EnqueueGenerateResourcePagesForRedis $enqueue,
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
