<?php

declare(strict_types=1);

use App\Email\Adapters\Craft\SendMailWithCraftMailer;
use App\Email\SendMailContract;
use craft\helpers\App;
use craft\mail\Mailer as CraftMailer;
use craft\models\MailSettings;

use function DI\autowire;

/**
 * @psalm-suppress MixedInferredReturnType
 * @psalm-suppress UndefinedClass
 */
return [
    CraftMailer::class => static function (): CraftMailer {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getMailer();
    },
    MailSettings::class => static function (): MailSettings {
        return App::mailSettings();
    },
    SendMailContract::class => autowire(
        SendMailWithCraftMailer::class,
    ),
];
