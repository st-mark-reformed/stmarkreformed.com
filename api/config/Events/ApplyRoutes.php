<?php

declare(strict_types=1);

namespace Config\Events;

use App\Contact\PostContactAction;
use App\HasCmsAccessAction;
use App\Healthcheck;
use App\Profiles\GetAllProfilesCmsAction;
use App\Profiles\PostNewProfile\PostCreateProfileCmsAction;
use BuzzingPixel\Queue\Http\Routes\Route;
use BuzzingPixel\Queue\Http\Routes\RoutesFactory as QueueRoutesFactory;
use Config\RuntimeConfig;
use Config\RuntimeConfigOptions;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function assert;

readonly class ApplyRoutes
{
    public function onDispatch(ApplyRoutesEvent $routes): void
    {
        Healthcheck::applyRoute($routes);
        PostContactAction::applyRoute($routes);
        HasCmsAccessAction::applyRoute($routes);
        PostCreateProfileCmsAction::applyRoute($routes);
        GetAllProfilesCmsAction::applyRoute($routes);

        $config = $routes->getContainer()->get(RuntimeConfig::class);

        assert($config instanceof RuntimeConfig);

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
