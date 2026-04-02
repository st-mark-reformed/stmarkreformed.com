<?php

declare(strict_types=1);

namespace Config\Events;

use App\GetIndexAction;
use App\GetWellKnownOpenIdConfiguration;
use App\Healthcheck;
use App\LogIn\PostLogInAction;
use App\LogOut\GetLogOutLanding;
use App\LogOut\LogOutAction;
use App\Oauth\Authorize\GetAuthorizeAction;
use App\Oauth\Authorize\GetAuthorizeTestAction;
use App\Oauth\PostTokenAction;
use App\User\GetUserInfoAction;
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
        GetWellKnownOpenIdConfiguration::applyRoute(routes: $routes);
        GetAuthorizeAction::applyRoute(routes: $routes);
        GetAuthorizeTestAction::applyRoute(routes: $routes);
        PostTokenAction::applyRoute(routes: $routes);
        GetUserInfoAction::applyRoute(routes: $routes);
    }
}
