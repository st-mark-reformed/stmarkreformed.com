<?php

declare(strict_types=1);

namespace App\Http\Response\News\CraftEvents;

use App\Http\Response\News\EnqueueGenerateNewsPagesForRedis;
use App\Http\Response\News\EnqueueGeneratePastorsPagesForRedis;
use craft\base\Element;
use yii\base\Event;

class SetUpNewsEvents
{
    public function __construct(
        private EnqueueGenerateNewsPagesForRedis $enqueueNews,
        private EnqueueGeneratePastorsPagesForRedis $enqueuePastors,
    ) {
    }

    public function setUp(): void
    {
        $newsHandler = [$this->enqueueNews, 'enqueue'];

        Event::on(
            Element::class,
            Element::EVENT_AFTER_SAVE,
            $newsHandler
        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_DELETE,
            $newsHandler
        );

        $pastorsHandler = [$this->enqueuePastors, 'enqueue'];

        Event::on(
            Element::class,
            Element::EVENT_AFTER_SAVE,
            $pastorsHandler
        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_DELETE,
            $pastorsHandler
        );
    }
}
