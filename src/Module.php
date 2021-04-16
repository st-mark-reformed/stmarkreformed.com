<?php

namespace src;

use BuzzingPixel\TwigDumper\TwigDumper;
use Craft;
use Exception;
use yii\base\Event;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\services\Utilities;
use src\services\CacheService;
use yii\base\Module as ModuleBase;
use src\services\EntrySlugService;
use src\services\EntryRoutingService;
use craft\events\SetElementRouteEvent;
use src\twigextensions\DevTwigExtensions;
use src\utilities\ImageTransformsUtility;
use craft\events\RegisterComponentTypesEvent;
use src\services\InitAssetTransformJobService;
use craft\console\Application as ConsoleApplication;

class Module extends ModuleBase
{
    /**
     * Initializes the module.
     * @throws Exception
     */
    public function init()
    {
        $this->setUp();

        $this->setEvents();

        $this->registerUtilityTypes();

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'src\commands';
        }

        parent::init();
    }

    /**
     * Sets up the module
     * @throws Exception
     */
    private function setUp()
    {
        Craft::setAlias('@root', dirname(__DIR__));

        Craft::setAlias('@dev', __DIR__);

        Craft::setAlias('@src', __DIR__);

        Craft::$app->view->registerTwigExtension(
            new DevTwigExtensions()
        );

        if (getenv('CLEAR_TEMPLATE_CACHE_ON_LOAD') === 'true') {
            (new CacheService())->clearTemplateCache();
        }

        if (! class_exists(TwigDumper::class)) {
            return;
        }

        Craft::$app->view->registerTwigExtension(new TwigDumper());
    }

    /**
     * Sets events
     * @throws Exception
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

        Event::on(
            Asset::class,
            Asset::EVENT_AFTER_SAVE,
            function (ModelEvent $eventModel) {
                /** @var Asset $asset */
                $asset = $eventModel->sender;

                // If this is not an image we can stop here
                if (! $asset->getHeight()) {
                    return;
                }

                (new InitAssetTransformJobService())->init((int) $asset->id);
            }
        );
    }

    private function registerUtilityTypes()
    {
        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ImageTransformsUtility::class;
            }
        );
    }
}
