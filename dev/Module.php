<?php

namespace dev;

use Craft;
use yii\base\Event;
use craft\elements\Entry;
use craft\events\ModelEvent;
use dev\services\CacheService;
use dev\services\EventSlugService;
use yii\base\Module as ModuleBase;
use dev\services\EntryRoutingService;
use craft\events\SetElementRouteEvent;
use dev\twigextensions\DevTwigExtensions;

/**
 * Custom module class for this project.
 *
 * This class will be available throughout the system via:
 * `Craft::$app->getModule('dev')`.
 *
 * If you want the module to get loaded on every request, uncomment this line
 * in config/app.php:
 *
 *     'bootstrap' => ['dev']
 *
 * Learn more about Yii module development in Yii's documentation:
 * http://www.yiiframework.com/doc-2.0/guide-structure-modules.html
 */
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
                $entryRoutingService->pageEntryRouteHandler($eventModel);
                $entryRoutingService->entryControllerRouting($eventModel);
            }
        );

        Event::on(
            Entry::class,
            Entry::EVENT_BEFORE_SAVE,
            function (ModelEvent $eventModel) {
                /** @var Entry $entry */
                $entry = $eventModel->sender;
                (new EventSlugService())->setEventEntrySlug($entry);
            }
        );
    }
}
