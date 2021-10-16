<?php

declare(strict_types=1);

use App\CustomErrorHandler;
use App\Module;

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
    'bootstrap' => ['dev'],
];

if ((bool) getenv('DEV_MODE') && mb_strtolower(PHP_SAPI) !== 'cli') {
    $config['components']['errorHandler'] = [
        'class' => CustomErrorHandler::class,
    ];
}

return $config;
