<?php

declare(strict_types=1);

use craft\config\GeneralConfig;
use craft\queue\Queue;
use craft\services\Elements as ElementsService;
use craft\services\Globals;

/**
 * @psalm-suppress MixedInferredReturnType
 * @psalm-suppress UndefinedClass
 */
return [
    ElementsService::class => static function (): ElementsService {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getElements();
    },
    GeneralConfig::class => static function (): GeneralConfig {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getConfig()->getGeneral();
    },
    Globals::class => static function (): Globals {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getGlobals();
    },
    Queue::class => static function (): Queue {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getQueue();
    },
];
