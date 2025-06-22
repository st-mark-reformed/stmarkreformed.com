<?php

declare(strict_types=1);

namespace Config\Events;

use App\Contact\PostContactAction;
use App\HasCmsAccessAction;
use App\Healthcheck;
use App\Messages\FileManager\DeleteFilesCmsAction;
use App\Messages\FileManager\ListAllFilesCmsAction;
use App\Messages\GetAllMessagesCmsAction;
use App\Messages\PostCreateMessageCmsAction;
use App\Messages\Series\DeleteMessageSeriesCmsAction;
use App\Messages\Series\GetAllMessageSeriesCmsAction;
use App\Messages\Series\GetMessageSeriesCmsAction;
use App\Messages\Series\PostCreateMessageSeriesCmsAction;
use App\Messages\Series\PutMessageSeriesCmsAction;
use App\Profiles\DeleteProfilesCmsAction;
use App\Profiles\GetAllProfilesCmsAction;
use App\Profiles\GetProfileCmsAction;
use App\Profiles\PostCreateProfileCmsAction;
use App\Profiles\PutProfileCmsAction;
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
        DeleteProfilesCmsAction::applyRoute($routes);
        GetProfileCmsAction::applyRoute($routes);
        PutProfileCmsAction::applyRoute($routes);
        PostCreateMessageSeriesCmsAction::applyRoute($routes);
        GetAllMessageSeriesCmsAction::applyRoute($routes);
        DeleteMessageSeriesCmsAction::applyRoute($routes);
        GetMessageSeriesCmsAction::applyRoute($routes);
        PutMessageSeriesCmsAction::applyRoute($routes);
        PostCreateMessageCmsAction::applyRoute($routes);
        ListAllFilesCmsAction::applyRoute($routes);
        DeleteFilesCmsAction::applyRoute($routes);
        GetAllMessagesCmsAction::applyRoute($routes);

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
