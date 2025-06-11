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

    case AUTH0_USER_INFO_URL;
    case AUTH0_WELL_KNOWN_URL;
    case AUTH0_CLIENT_ID;
    case AUTH0_CLIENT_SECRET;
    case AUTH0_CALLBACK_DOMAIN;

    case API_DB_HOST;
    case API_DB_NAME;
    case API_DB_USER;
    case API_DB_PASSWORD;
    case API_DB_PORT;

    case ROOT_DB_HOST;
    case ROOT_DB_USER;
    case ROOT_DB_PASSWORD;
    case ROOT_DB_PORT;
}
