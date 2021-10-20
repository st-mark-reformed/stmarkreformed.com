<?php

declare(strict_types=1);

namespace App;

use App\Craft\SetMessageEntrySlug\SetMessageEntrySlugFactory;
use BuzzingPixel\TwigDumper\TwigDumper;
use Config\di\Container;
use Config\Twig;
use Craft;
use craft\base\Element;
use craft\console\Application as ConsoleApplication;
use craft\events\ModelEvent;
use Exception;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;
use yii\base\Event;
use yii\base\Module as ModuleBase;

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
     * @throws Exception
     *
     * @psalm-suppress UndefinedClass
     */
    private function setUp(): void
    {
        $di = Container::get();

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

        /** @phpstan-ignore-next-line */
        if (! Craft::$app->getRequest()->getIsCpRequest()) {
            /** @phpstan-ignore-next-line */
            Craft::$app->getView()->getTwig()->setLoader(
                $di->get(FilesystemLoader::class),
            );
        }

        foreach (Twig::EXTENSIONS as $extClassString) {
            /** @psalm-suppress UndefinedMethod */
            if (
                method_exists(
                    $extClassString,
                    'shouldAddExtension'
                ) &&
                ! $extClassString::shouldAddExtension()
            ) {
                continue;
            }

            /** @psalm-suppress MixedAssignment */
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
    }

    /**
     * @psalm-suppress UndefinedClass
     */
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
        $this->controllerMap = [];
    }

    private function mapWebControllers(): void
    {
        $this->controllerMap = [];
    }
}
