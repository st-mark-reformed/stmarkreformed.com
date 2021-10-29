<?php

declare(strict_types=1);

namespace App\Craft;

use App\Http\Utility\ClearStaticCache;
use craft\base\Element;
use yii\base\Event;

use function assert;

class ElementSaveClearStaticCache
{
    public function __construct(private ClearStaticCache $clearStaticCache)
    {
    }

    public function clear(Event $event): void
    {
        $element = $event->sender;

        assert($element instanceof Element);

        if ($element->getIsDraft()) {
            return;
        }

        $this->clearStaticCache->clear();
    }
}
