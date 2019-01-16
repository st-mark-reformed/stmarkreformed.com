<?php
declare(strict_types=1);

$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
$protocol = $secure ? 'https://' : 'http://';

return [
    '*' => [
        'allowUpdates' => false,
        'appId' => 'stmarkreformed',
        'cacheDuration' => 0,
        'cacheMethod' => 'apc',
        'basePath' => CRAFT_BASE_PATH,
        'cpTrigger' => 'cms',
        'devMode' => getenv('DEV_MODE') === 'true',
        'enableCsrfProtection' => true,
        'errorTemplatePrefix' => '_errors/',
        'generateTransformsBeforePageLoad' => true,
        'isSystemOn' => true,
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
        'userSessionDuration' => false, // As long as browser stays open
        'staticAssetCacheTime' => '',
    ],
];
