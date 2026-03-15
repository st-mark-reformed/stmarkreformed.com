<?php

declare(strict_types=1);

namespace Config\Events;

use App\Cookies\SetCookiesMiddleware;
use App\ExceptionHandling\OauthRequestExceptionHandlerMiddleware;
use BuzzingPixel\Minify\MinifyMiddleware;
use RxAnte\AppBootstrap\Http\ApplyMiddlewareEvent;

class ApplyMiddlewareEventSubscriber
{
    public function onDispatch(ApplyMiddlewareEvent $middleware): void
    {
        $middleware->add(MinifyMiddleware::class);
        $middleware->add(SetCookiesMiddleware::class);
        $middleware->add(OauthRequestExceptionHandlerMiddleware::class);
    }
}
