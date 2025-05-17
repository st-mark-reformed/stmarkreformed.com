<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\CraftEvents;

use App\Http\Response\Members\InternalMedia\EnqueueGenerateInternalMediaPagesForRedis;
use craft\base\Element;
use yii\base\Event;

class SetUpInternalAudioEvents
{
    public function __construct(
        private EnqueueGenerateInternalMediaPagesForRedis $enqueue,
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
