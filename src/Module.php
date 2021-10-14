<?php

declare(strict_types=1);

namespace App;

use BuzzingPixel\TwigDumper\TwigDumper;
use Craft;
use craft\console\Application as ConsoleApplication;
use Exception;
use yii\base\Module as ModuleBase;

use function class_exists;
use function dirname;
use function getenv;

/**
 * @codeCoverageIgnore
 */
class Module extends ModuleBase
{
    /**
     * Initializes the module.
     *
     * @throws Exception
     */
    public function init(): void
    {
        $this->setUp();

        $this->setEvents();

        // Add in our console commands
        // TODO: Refactor this
        // if (Craft::$app instanceof ConsoleApplication) {
        //     $this->controllerNamespace = 'src\commands';
        // }

        parent::init();
    }

    /**
     * Sets up the module
     *
     * @throws Exception
     *
     * @psalm-suppress UndefinedClass
     */
    private function setUp(): void
    {
        /** @phpstan-ignore-next-line */
        Craft::setAlias('@root', dirname(__DIR__));

        /** @phpstan-ignore-next-line */
        Craft::setAlias('@App', __DIR__);

        /** @phpstan-ignore-next-line */
        Craft::setAlias('@dev', __DIR__);

        /** @phpstan-ignore-next-line */
        Craft::setAlias('@src', __DIR__);

        $secure   = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $protocol = $secure ? 'https://' : 'http://';

        /** @phpstan-ignore-next-line */
        if (Craft::$app instanceof ConsoleApplication) {
            /** @phpstan-ignore-next-line */
            Craft::setAlias(
                '@siteUrl',
                getenv('SITE_URL'),
            );
        } else {
            /** @phpstan-ignore-next-line */
            Craft::setAlias(
                '@siteUrl',
                getenv('USE_HTTP_HOST_FOR_SITE_URL') === 'true' ?
                    $protocol . $_SERVER['HTTP_HOST'] :
                    getenv('SITE_URL'),
            );
        }

        if (! class_exists(TwigDumper::class)) {
            return;
        }

        /** @phpstan-ignore-next-line */
        Craft::$app->view->registerTwigExtension(new TwigDumper());
    }

    /**
     * Sets events
     *
     * @throws Exception
     */
    private function setEvents(): void
    {
        // TODO: Replace this
        // Event::on(
        //     Entry::class,
        //     Entry::EVENT_SET_ROUTE,
        //     function (SetElementRouteEvent $eventModel) {
        //         $entryRoutingService = new EntryRoutingService();
        //         $entryRoutingService->entryControllerRouting($eventModel);
        //     }
        // );

        // TODO: Replace this
        // Event::on(
        //     Entry::class,
        //     Entry::EVENT_BEFORE_SAVE,
        //     function (ModelEvent $eventModel) {
        //         /** @var Entry $entry */
        //         $entry = $eventModel->sender;
        //         (new EntrySlugService())->setEventEntrySlug($entry);
        //         (new EntrySlugService())->setMessageEntrySlug($entry);
        //     }
        // );
    }
}
