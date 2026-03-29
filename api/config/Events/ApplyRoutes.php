<?php

declare(strict_types=1);

namespace Config\Events;

use App\Contact\PostContactAction;
use App\GetKeepAliveAction;
use App\Healthcheck;
use App\Messages\Admin\GetHasEditMessagesRoleAction;
use App\Messages\Admin\NewMessage\PostNewMessageAction;
use App\Profiles\Admin\EditProfile\GetEditProfile\GetEditProfileAction;
use App\Profiles\Admin\EditProfile\PostEditProfile\PostEditProfileAction;
use App\Profiles\Admin\GetHasEditProfilesRoleAction;
use App\Profiles\Admin\GetLeadershipPositionsAction;
use App\Profiles\Admin\GetProfilesListAction;
use App\Profiles\Admin\NewProfile\PostNewProfileAction;
use App\Series\Admin\EditSeries\GetEditSeries\GetEditSeriesAction;
use App\Series\Admin\EditSeries\PostEditSeries\PostEditSeriesAction;
use App\Series\Admin\GetSeriesListAction;
use App\Series\Admin\NewSeries\PostNewSeriesAction;
use App\Tinker;
use BuzzingPixel\Queue\Http\Routes\Route;
use BuzzingPixel\Queue\Http\Routes\RoutesFactory as QueueRoutesFactory;
use Config\RuntimeConfigOptions;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\RuntimeConfig;

use function assert;

readonly class ApplyRoutes
{
    public function onDispatch(ApplyRoutesEvent $routes): void
    {
        $config = $routes->getContainer()->get(RuntimeConfig::class);
        assert($config instanceof RuntimeConfig);

        if ($config->getBoolean(RuntimeConfigOptions::DEV_MODE)) {
            Tinker::applyRoute(routes: $routes);
        }

        Healthcheck::applyRoute(routes: $routes);
        PostContactAction::applyRoute(routes: $routes);
        GetProfilesListAction::applyRoute(routes: $routes);
        GetLeadershipPositionsAction::applyRoute(routes: $routes);
        GetHasEditProfilesRoleAction::applyRoute(routes: $routes);
        PostNewProfileAction::applyRoute(routes: $routes);
        GetKeepAliveAction::applyRoute(routes: $routes);
        GetEditProfileAction::applyRoute(routes: $routes);
        PostEditProfileAction::applyRoute(routes: $routes);
        GetHasEditMessagesRoleAction::applyRoute(routes: $routes);
        PostNewSeriesAction::applyRoute(routes: $routes);
        GetSeriesListAction::applyRoute(routes: $routes);
        GetEditSeriesAction::applyRoute(routes: $routes);
        PostEditSeriesAction::applyRoute(routes: $routes);
        PostNewMessageAction::applyRoute(routes: $routes);

        if (
            ! $config->getBoolean(
                RuntimeConfigOptions::ENABLE_QUEUE_MANAGEMENT_ROUTES,
            )
        ) {
            return;
        }

        $this->createQueueInterfaceRoutes($routes);
    }

    private function createQueueInterfaceRoutes(ApplyRoutesEvent $routes): void
    {
        $queueRoutesFactory = $routes->getContainer()->get(
            QueueRoutesFactory::class,
        );

        assert($queueRoutesFactory instanceof QueueRoutesFactory);

        $queueRoutes = $queueRoutesFactory->create();

        $queueRoutes->map(
            static function (Route $route) use ($routes): void {
                $routes->map(
                    [$route->requestMethod->name],
                    $route->pattern,
                    $route->class,
                );
            },
        );
    }
}
