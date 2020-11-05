<?php
declare(strict_types=1);

$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
$protocol = $secure ? 'https://' : 'http://';

return [
    '*' => [
        // 'allowAdminChanges' => getenv('ALLOW_ADMIN_CHANGES') === 'true',
        'allowUpdates' => false,
        'appId' => 'stmarkreformed',
        'backupOnUpdate' => getenv('BACKUP_DB_ON_UPDATE') !== 'false',
        'cacheDuration' => 0,
        'cacheMethod' => 'apc',
        'basePath' => CRAFT_BASE_PATH,
        'cpTrigger' => 'cms',
        'devMode' => getenv('DEV_MODE') === 'true',
        'errorTemplatePrefix' => '_errors/',
        'generateTransformsBeforePageLoad' => true,
        'isSystemLive' => true,
        'maxUploadFileSize' => 512000000,
        'omitScriptNameInUrls' => true,
        'postCpLoginRedirect' => 'entries',
        'projectPath' => CRAFT_BASE_PATH,
        'rememberedUserSessionDuration' => 'P100Y', // 100 years
        'runQueueAutomatically' => getenv('DISABLE_AUTOMATIC_QUEUE') !== 'true',
        'securityKey' => getenv('SECURITY_KEY'),
        'sendPoweredByHeader' => false,
        'siteName' => 'St. Mark Reformed Church',
        'siteUrl' => getenv('USE_HTTP_HOST_FOR_SITE_URL') === 'true' ?
            $protocol . $_SERVER['HTTP_HOST'] :
            getenv('SITE_URL'),
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
