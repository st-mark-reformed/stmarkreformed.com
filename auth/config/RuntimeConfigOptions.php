<?php

declare(strict_types=1);

namespace Config;

enum RuntimeConfigOptions
{
    case USE_WHOOPS_ERROR_HANDLING;
    case USE_PRODUCTION_ERROR_MIDDLEWARE;

    case APP_URL;

    // Cache
    case REDIS_HOST;

    // Cookies
    case COOKIE_ENCRYPTION_KEY;

    // Oauth
    // Created from ./vendor/bin/generate-defuse-key
    case OAUTH_ENCRYPTION_KEY;
    case RSA_PRIVATE_KEY;
    case RSA_PUBLIC_KEY;
    case SMRC_CLIENT_REDIRECT_URI;
    case SMRC_CLIENT_SECRET;

    // Auth DB
    case SMRC_AUTH_DB_HOST;
    case SMRC_AUTH_DB_NAME;
    case SMRC_AUTH_DB_USER;
    case SMRC_AUTH_DB_PASSWORD;
}
