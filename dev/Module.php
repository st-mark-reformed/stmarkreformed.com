<?php

namespace dev;

use Craft;
use yii\base\Event;
use craft\elements\Entry;
use craft\events\ModelEvent;
use dev\services\CacheService;
use dev\services\EntrySlugService;
use yii\base\Module as ModuleBase;
use dev\services\EntryRoutingService;
use craft\events\SetElementRouteEvent;
use dev\twigextensions\DevTwigExtensions;
use craft\console\Application as ConsoleApplication;

class Module extends ModuleBase
{
    /**
     * Initializes the module.
     * @throws \Exception
     */
    public function init()
    {
        $this->setUp();
        $this->setEvents();

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'dev\commands';
        }

        parent::init();
    }

    /**
     * Sets up the module
     * @throws \Exception
     */
    private function setUp()
    {
        Craft::setAlias('@dev', __DIR__);

        Craft::$app->view->registerTwigExtension(
            new DevTwigExtensions()
        );

        if (getenv('CLEAR_TEMPLATE_CACHE_ON_LOAD') === 'true') {
            (new CacheService())->clearTemplateCache();
        }
    }

    /**
     * Sets events
     * @throws \Exception
     */
    private function setEvents()
    {
        Event::on(
            Entry::class,
            Entry::EVENT_SET_ROUTE,
            function (SetElementRouteEvent $eventModel) {
                $entryRoutingService = new EntryRoutingService();
                $entryRoutingService->entryControllerRouting($eventModel);
            }
        );

        Event::on(
            Entry::class,
            Entry::EVENT_BEFORE_SAVE,
            function (ModelEvent $eventModel) {
                /** @var Entry $entry */
                $entry = $eventModel->sender;
                (new EntrySlugService())->setEventEntrySlug($entry);
                (new EntrySlugService())->setMessageEntrySlug($entry);
            }
        );
    }
}
