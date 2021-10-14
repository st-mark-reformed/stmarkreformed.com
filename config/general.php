<?php
declare(strict_types=1);

/** @psalm-suppress UndefinedConstant */
$craftBasePath = (string) CRAFT_BASE_PATH;

return [
    '*' => [
        // 'allowAdminChanges' => getenv('ALLOW_ADMIN_CHANGES') === 'true',
        'allowUpdates' => false,
        'appId' => 'stmarkreformed',
        'backupOnUpdate' => getenv('BACKUP_DB_ON_UPDATE') !== 'false',
        'cacheDuration' => 0,
        'cacheMethod' => 'apc',
        'basePath' => $craftBasePath,
        'cpTrigger' => 'cms',
        'devMode' => getenv('DEV_MODE') === 'true',
        'errorTemplatePrefix' => '_errors/',
        'generateTransformsBeforePageLoad' => true,
        'isSystemLive' => true,
        'maxUploadFileSize' => 512000000,
        'omitScriptNameInUrls' => true,
        'postCpLoginRedirect' => 'entries',
        'projectPath' => $craftBasePath,
        'rememberedUserSessionDuration' => 'P100Y', // 100 years
        'runQueueAutomatically' => getenv('DISABLE_AUTOMATIC_QUEUE') !== 'true',
        'securityKey' => getenv('SECURITY_KEY'),
        'sendPoweredByHeader' => false,
        'suppressTemplateErrors' => getenv('DEV_MODE') !== 'true',
        'timezone' => 'America/Chicago',
        'useEmailAsUsername' => true,
        // 'useProjectConfigFile' => true,
        'userSessionDuration' => false, // As long as browser stays open
        'staticAssetCacheTime' => '',

        'stripePayments' => [
            'livePublishableKey' => getenv('STRIPE_PUBLISHABLE_KEY'),
            'liveSecretKey' => getenv('STRIPE_SECRET_KEY'),
            'testPublishableKey' => getenv('STRIPE_PUBLISHABLE_KEY'),
            'testSecretKey' =>  getenv('STRIPE_SECRET_KEY'),
            'testMode' => getenv('STRIPE_TEST_MODE') === 'true' ? 1 : 0,
        ],
    ],
];
