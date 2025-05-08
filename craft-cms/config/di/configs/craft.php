<?php

declare(strict_types=1);

use craft\config\GeneralConfig;
use craft\queue\Queue;
use craft\services\Elements as ElementsService;
use craft\services\Globals;
use craft\services\Security;
use craft\services\Users;
use craft\web\User;

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
    User::class => static function (): User {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getUser();
    },
    Users::class => static function (): Users {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getUsers();
    },
    Security::class => static function (): Security {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getSecurity();
    },
];
