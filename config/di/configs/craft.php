<?php

declare(strict_types=1);

use craft\queue\Queue;
use craft\services\Globals;

/**
 * @psalm-suppress MixedInferredReturnType
 * @psalm-suppress UndefinedClass
 */
return [
    Globals::class => static function (): Globals {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getGlobals();
    },
    Queue::class => static function (): Queue {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getQueue();
    },
];
