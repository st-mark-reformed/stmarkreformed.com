<?php
declare(strict_types=1);

use App\Module;
use craft\helpers\App;
use App\CustomErrorHandler;
use craft\mail\transportadapters\Smtp;

/**
 * @psalm-suppress UnusedVariable
 * @psalm-suppress UndefinedClass
 * @psalm-suppress MixedReturnStatement
 * @psalm-suppress MixedInferredReturnType
 */
$config = [
    'modules' => [
        'dev' => Module::class,
    ],
    'bootstrap' => [
        'dev'
    ],
    'components' => [
        'mailer' => function (): object {
            $settings = App::mailSettings();

            $settings->fromEmail = 'info@stmarkreformed.com';

            $settings->fromName = 'St. Mark Reformed Church';

            $settings->template = '';

            $settings->transportType = Smtp::class;

            $settings->transportSettings = [
                'encryptionMethod' => 'tls',
                'host' => 'smtp.mandrillapp.com',
                'port' => '587',
                'timeout' => '30',
                'useAuthentication' => '1',
                'username' => 'info@buzzingpixel.com',
                'password' => getenv('SMTP_PASSWORD'),
            ];

            $config = App::mailerConfig($settings);

            return Craft::createObject($config);
        },
    ],
];

if (getenv('DEV_MODE') === 'true' && strtolower(PHP_SAPI) !== 'cli') {
    $config['components']['errorHandler'] = [
        'class' => CustomErrorHandler::class,
    ];
}

return $config;
