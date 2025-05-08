<?php

declare(strict_types=1);

use App\CustomErrorHandler;
use App\Module;
use craft\behaviors\SessionBehavior;
use yii\redis\Cache;
use yii\redis\Connection;
use yii\redis\Session;

$config = [
    'modules' => [
        'dev' => Module::class,
    ],
    'bootstrap' => ['dev'],
    'components' => [
        'redis' => [
            'class' => Connection::class,
            'hostname' => (string) getenv('REDIS_HOST'),
            // 'port' => getenv('REDIS_PORT'),
            // 'password' => getenv('REDIS_PASSWORD'),
        ],
        'cache' => [
            'class' => Cache::class,
            'defaultDuration' => 86400,
            'keyPrefix' => getenv('CRAFT_REDIS_KEY_PREFIX'),
        ],
        'session' => [
            'class' => Session::class,
            'as session' => SessionBehavior::class,
        ],
    ],
];

if ((bool) getenv('DEV_MODE') && mb_strtolower(PHP_SAPI) !== 'cli') {
    $config['components']['errorHandler'] = [
        'class' => CustomErrorHandler::class,
    ];
}

return $config;
