<?php

declare(strict_types=1);

namespace App;

use App\Craft\Behaviors\ProfileEntriesBehavior;
use App\Craft\Commands\ElasticSearchConsoleController;
use App\Craft\Commands\MessagesConsoleController;
use App\Craft\Commands\ProfilesConsoleController;
use App\Craft\ElementSaveClearStaticCache;
use App\Craft\SetMessageEntrySlug\SetMessageEntrySlugFactory;
use App\ElasticSearch\Events\ModifyElementQueueIndexAllMessages;
use App\Http\Response\Media\Messages\CraftEvents\SetUpMessagesEvents;
use App\Http\Response\Media\Resources\CraftEvents\SetUpResourcesEvents;
use App\Http\Response\Media\Resources\GenerateResourcePagesForRedis;
use App\Http\Response\Members\HymnsOfTheMonth\CraftEvents\SetUpHymnsOfTheMonthEvents;
use App\Http\Response\Members\InternalMedia\CraftEvents\SetUpInternalAudioEvents;
use App\Http\Response\Publications\CraftEvents\SetUpMenOfTheMarkEvents;
use App\Http\Response\News\CraftEvents\SetUpNewsEvents;
use App\Http\Utility\ClearStaticCache;
use App\Messages\Events\ModifyElementQueueSetMessageSeriesLatestEntry;
use App\Profiles\Events\ModifyElementQueueSetHasMessagesOnAllProfiles;
use App\Templating\TwigControl\TwigControl;
use BuzzingPixel\CraftScheduler\ContainerRetrieval\RetrieveContainers;
use BuzzingPixel\CraftScheduler\CraftSchedulerPlugin;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\RetrieveSchedule;
use BuzzingPixel\TwigDumper\TwigDumper;
use Config\di\Container;
use Config\Schedule;
use Config\Twig;
use Craft;
use craft\base\Element;
use craft\base\Model;
use craft\console\Application as ConsoleApplication;
use craft\elements\Entry;
use craft\events\DefineBehaviorsEvent;
use craft\events\ModelEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\utilities\ClearCaches;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig\Extension\ExtensionInterface;
use yii\base\Event;
use yii\base\Module as ModuleBase;

use function array_merge;
use function assert;
use function class_exists;
use function getenv;
use function method_exists;

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

        $this->mapControllers();

        parent::init();
    }

    /**
     * Sets up the module
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function setUp(): void
    {
        $di = Container::get();

        /** @phpstan-ignore-next-line */
        Craft::setAlias('@basePath', CRAFT_BASE_PATH);

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

        $twigControl = $di->get(TwigControl::class);

        assert($twigControl instanceof TwigControl);

        /** @phpstan-ignore-next-line */
        if (! Craft::$app->getRequest()->getIsCpRequest()) {
            $twigControl->useCustomTwigLoader();
        }

        foreach (Twig::globals(di: $di) as $name => $val) {
            /** @phpstan-ignore-next-line */
            Craft::$app->getView()->getTwig()->addGlobal(
                $name,
                $val,
            );
        }

        foreach (Twig::EXTENSIONS as $extClassString) {
            if (
                method_exists(
                    $extClassString,
                    'shouldAddExtension'
                ) &&
                ! $extClassString::shouldAddExtension()
            ) {
                continue;
            }

            $ext = $di->get($extClassString);

            assert($ext instanceof ExtensionInterface);

            /** @phpstan-ignore-next-line */
            Craft::$app->getView()->registerTwigExtension($ext);
        }

        if (! class_exists(TwigDumper::class)) {
            return;
        }

        /** @phpstan-ignore-next-line */
        Craft::$app->getView()->registerTwigExtension(
            new TwigDumper(),
        );
    }

    /**
     * Sets events
     *
     * @throws Exception
     */
    private function setEvents(): void
    {
        $di = Container::get();

        $clearStaticCache = $di->get(ElementSaveClearStaticCache::class);

        assert(
            $clearStaticCache instanceof ElementSaveClearStaticCache
        );

        $modifyElementQueueIndexAllMessages = $di->get(
            ModifyElementQueueIndexAllMessages::class
        );

        assert(
            $modifyElementQueueIndexAllMessages instanceof
                ModifyElementQueueIndexAllMessages
        );

        $modifyElementQueueSetMessageSeriesLatestEntry = $di->get(
            ModifyElementQueueSetMessageSeriesLatestEntry::class,
        );

        assert(
            $modifyElementQueueSetMessageSeriesLatestEntry instanceof
                ModifyElementQueueSetMessageSeriesLatestEntry
        );

        $modifyElementQueueSetHasMessagesOnAllProfiles = $di->get(
            ModifyElementQueueSetHasMessagesOnAllProfiles::class,
        );

        assert(
            $modifyElementQueueSetHasMessagesOnAllProfiles instanceof
                ModifyElementQueueSetHasMessagesOnAllProfiles
        );

        Event::on(
            Element::class,
            Element::EVENT_BEFORE_SAVE,
            static function (ModelEvent $eventModel): void {
                $factory = Container::get()->get(
                    SetMessageEntrySlugFactory::class
                );

                assert(
                    $factory instanceof SetMessageEntrySlugFactory
                );

                $factory->make(eventModel: $eventModel)->set();
            }
        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_SAVE,
            [$modifyElementQueueIndexAllMessages, 'respond'],
        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_DELETE,
            [$modifyElementQueueIndexAllMessages, 'respond'],
        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_SAVE,
            [
                $modifyElementQueueSetMessageSeriesLatestEntry,
                'respond',
            ],
        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_DELETE,
            [
                $modifyElementQueueSetMessageSeriesLatestEntry,
                'respond',
            ],
        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_SAVE,
            [
                $modifyElementQueueSetHasMessagesOnAllProfiles,
                'respond',
            ],
        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_DELETE,
            [
                $modifyElementQueueSetHasMessagesOnAllProfiles,
                'respond',
            ],
        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_SAVE,
            [$clearStaticCache, 'clear'],
        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_DELETE,
            [$clearStaticCache, 'clear'],
        );

        Event::on(
            ClearCaches::class,
            ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
            static function (RegisterCacheOptionsEvent $event): void {
                $di = Container::get();

                $clearStaticCache = $di->get(ClearStaticCache::class);

                assert($clearStaticCache instanceof ClearStaticCache);

                $event->options[] = [
                    'key' => 'static-caches',
                    'label' => 'Static Caches',
                    'action' => [$clearStaticCache, 'clear'],
                ];
            }
        );

        Event::on(
            Entry::class,
            Model::EVENT_DEFINE_BEHAVIORS,
            static function (DefineBehaviorsEvent $event): void {
                $event->behaviors = array_merge(
                    $event->behaviors,
                    [
                        'profileFullNameWithHonorific' => ProfileEntriesBehavior::class,
                    ],
                );
            }
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $schedule = $di->get(Schedule::class);
        assert($schedule instanceof Schedule);

        Event::on(
            RetrieveContainers::class,
            RetrieveContainers::EVENT_RETRIEVE_CONTAINERS,
            [$schedule, 'retrieveContainers'],
        );

        Event::on(
            CraftSchedulerPlugin::class,
            CraftSchedulerPlugin::EVEN_SET_DEFAULT_CONTAINER,
            [$schedule, 'setDefaultContainer'],
        );

        Event::on(
            RetrieveSchedule::class,
            RetrieveSchedule::EVENT_RETRIEVE_SCHEDULE,
            [$schedule, 'retrieve'],
        );

        /**
         * SetUpInternalAudioEvents
         */
        $setUpInternalAudioEvents = $di->get(SetUpInternalAudioEvents::class);
        assert($setUpInternalAudioEvents instanceof SetUpInternalAudioEvents);
        $setUpInternalAudioEvents->setUp();

        /**
         * SetUpHymnsOfTheMonthEvents
         */
        $setUpHymnsOfTheMonthEvents = $di->get(SetUpHymnsOfTheMonthEvents::class);
        assert($setUpHymnsOfTheMonthEvents instanceof SetUpHymnsOfTheMonthEvents);
        $setUpHymnsOfTheMonthEvents->setUp();

        /**
         * SetUpNewsEvents
         */
        $setUpNewsEvents = $di->get(SetUpNewsEvents::class);
        assert($setUpNewsEvents instanceof SetUpNewsEvents);
        $setUpNewsEvents->setUp();

        /**
         * SetUpMenOfTheMarkEvents
         */
        $setUpMenOfTheMarkEvents = $di->get(SetUpMenOfTheMarkEvents::class);
        assert($setUpMenOfTheMarkEvents instanceof SetUpMenOfTheMarkEvents);
        $setUpMenOfTheMarkEvents->setUp();

        /**
         * SetUpResourcesEvents
         */
        $setUpResourceEvents = $di->get(SetUpResourcesEvents::class);
        assert($setUpResourceEvents instanceof SetUpResourcesEvents);
        $setUpResourceEvents->setUp();

        /**
         * SetUpMessagesEvents
         */
        $setUpMessagesEvents = $di->get(SetUpMessagesEvents::class);
        assert($setUpMessagesEvents instanceof SetUpMessagesEvents);
        $setUpMessagesEvents->setUp();
    }

    private function mapControllers(): void
    {
        /** @phpstan-ignore-next-line */
        if (Craft::$app instanceof ConsoleApplication) {
            $this->mapConsoleControllers();

            return;
        }

        $this->mapWebControllers();
    }

    private function mapConsoleControllers(): void
    {
        $this->controllerMap = [
            'elastic-search' => ElasticSearchConsoleController::class,
            'messages' => MessagesConsoleController::class,
            'profiles' => ProfilesConsoleController::class,
        ];
    }

    private function mapWebControllers(): void
    {
        $this->controllerMap = [];
    }
}
