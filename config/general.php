<?php

declare(strict_types=1);

/**
 * @psalm-suppress UndefinedConstant
 * @phpstan-ignore-next-line
 */
$craftBasePath = (string) CRAFT_BASE_PATH;

$devMode = (bool) getenv('DEV_MODE');

return [
    '*' => [
        'allowAdminChanges' => (bool) getenv('ALLOW_ADMIN_CHANGES'),
        'allowUpdates' => false,
        'appId' => 'stmarkreformed',
        'backupOnUpdate' => (bool) getenv('BACKUP_DB_ON_UPDATE'),
        'cacheDuration' => 0,
        'cacheMethod' => 'apc',
        'basePath' => $craftBasePath,
        'cpTrigger' => 'cms',
        'devMode' => $devMode,
        'errorTemplatePrefix' => '_errors/',
        'generateTransformsBeforePageLoad' => true,
        'isSystemLive' => true,
        'maxUploadFileSize' => 512000000,
        'omitScriptNameInUrls' => true,
        'postCpLoginRedirect' => 'entries',
        'projectPath' => $craftBasePath,
        'rememberedUserSessionDuration' => 'P100Y', // 100 years
        'runQueueAutomatically' => (bool) getenv('DISABLE_AUTOMATIC_QUEUE'),
        'securityKey' => getenv('SECURITY_KEY'),
        'sendPoweredByHeader' => false,
        'suppressTemplateErrors' => $devMode,
        'timezone' => 'America/Chicago',
        'useEmailAsUsername' => true,
        'useProjectConfigFile' => true,
        'userSessionDuration' => false, // As long as browser stays open
        'staticAssetCacheTime' => '',

        'stripePayments' => [
            'livePublishableKey' => getenv('STRIPE_PUBLISHABLE_KEY'),
            'liveSecretKey' => getenv('STRIPE_SECRET_KEY'),
            'testPublishableKey' => getenv('STRIPE_PUBLISHABLE_KEY'),
            'testSecretKey' =>  getenv('STRIPE_SECRET_KEY'),
            'testMode' => (bool) getenv('STRIPE_TEST_MODE') ? 1 : 0,
        ],
    ],
];
