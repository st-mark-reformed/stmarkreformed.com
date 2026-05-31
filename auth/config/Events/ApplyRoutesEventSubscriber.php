<?php

declare(strict_types=1);

namespace Config\Events;

use App\GetIndexAction;
use App\GetWellKnownOpenIdConfiguration;
use App\Healthcheck;
use App\LogIn\PostLogInAction;
use App\LogOut\GetLogOutLanding;
use App\LogOut\LogOutAction;
use App\ManagePassword\GetManagePasswordAction;
use App\ManagePassword\PostManagePasswordAction;
use App\ManageUsers\GetCreateUserAction;
use App\ManageUsers\GetEditUserAction;
use App\ManageUsers\GetManageUsersAction;
use App\ManageUsers\GetResetPasswordAction;
use App\ManageUsers\PostCreateUserAction;
use App\ManageUsers\PostDeleteUserAction;
use App\ManageUsers\PostEditUserAction;
use App\ManageUsers\PostResetPasswordAction;
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
        GetManagePasswordAction::applyRoute(routes: $routes);
        PostManagePasswordAction::applyRoute(routes: $routes);
        GetManageUsersAction::applyRoute(routes: $routes);
        GetCreateUserAction::applyRoute(routes: $routes);
        PostCreateUserAction::applyRoute(routes: $routes);
        GetEditUserAction::applyRoute(routes: $routes);
        PostEditUserAction::applyRoute(routes: $routes);
        GetResetPasswordAction::applyRoute(routes: $routes);
        PostResetPasswordAction::applyRoute(routes: $routes);
        PostDeleteUserAction::applyRoute(routes: $routes);
        GetWellKnownOpenIdConfiguration::applyRoute(routes: $routes);
        GetAuthorizeAction::applyRoute(routes: $routes);
        GetAuthorizeTestAction::applyRoute(routes: $routes);
        PostTokenAction::applyRoute(routes: $routes);
        GetUserInfoAction::applyRoute(routes: $routes);
    }
}
