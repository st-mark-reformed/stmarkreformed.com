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

    case ENABLE_QUEUE_MANAGEMENT_ROUTES;

    // Email
    case SMTP_USER;
    case SMTP_PASSWORD;
    case SMTP_HOST;
    case SMTP_PORT;
    case SYSTEM_EMAIL_FROM_ADDRESS;
    case SYSTEM_EMAIL_FROM_NAME;
    case CONTACT_FORM_RECIPIENTS;
}
