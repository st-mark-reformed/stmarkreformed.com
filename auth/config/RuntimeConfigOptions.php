<?php

declare(strict_types=1);

namespace Config;

enum RuntimeConfigOptions
{
    case USE_WHOOPS_ERROR_HANDLING;
    case USE_PRODUCTION_ERROR_MIDDLEWARE;

    // Cache
    case REDIS_HOST;

    // Cookies
    case COOKIE_ENCRYPTION_KEY;

    // Oauth
    // Created from ./vendor/bin/generate-defuse-key
    case OAUTH_ENCRYPTION_KEY;
}
