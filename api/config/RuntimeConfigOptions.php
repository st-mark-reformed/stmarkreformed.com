<?php

declare(strict_types=1);

namespace Config;

enum RuntimeConfigOptions
{
    case DEV_MODE;

    case USE_WHOOPS_ERROR_HANDLING;
    case USE_PRODUCTION_ERROR_MIDDLEWARE;

    case REDIS_HOST;

    case LOG_HANDLER_FACTORIES;
}
