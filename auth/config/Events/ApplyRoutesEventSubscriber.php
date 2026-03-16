<?php

declare(strict_types=1);

namespace Config\Events;

use App\GetIndexAction;
use App\Healthcheck;
use App\LogIn\PostLogInAction;
use App\LogOut\GetLogOutLanding;
use App\LogOut\LogOutAction;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

class ApplyRoutesEventSubscriber
{
    public function onDispatch(ApplyRoutesEvent $routes): void
    {
        Healthcheck::applyRoute(routes: $routes);
        GetIndexAction::applyRoute(routes: $routes);
        PostLogInAction::applyRoute(routes: $routes);
        LogOutAction::applyRoute(routes: $routes);
        GetLogOutLanding::applyRoute(routes: $routes);
    }
}
