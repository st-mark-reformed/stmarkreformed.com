<?php

declare(strict_types=1);

use Config\RuntimeConfigOptions;
use RxAnte\AppBootstrap\RuntimeConfig;

$config = new RuntimeConfig();

$dbName = $config->getString(
    RuntimeConfigOptions::SMRC_AUTH_DB_NAME,
);

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/config/Data/Migrations',
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_environment' => $dbName,
        $dbName => [
            'adapter' => 'mysql',
            'host' => $config->getString(
                RuntimeConfigOptions::SMRC_AUTH_DB_HOST,
            ),
            'name' => $dbName,
            'user' => $config->getString(
                RuntimeConfigOptions::SMRC_AUTH_DB_USER,
            ),
            'pass' =>  $config->getString(
                RuntimeConfigOptions::SMRC_AUTH_DB_PASSWORD,
            ),
            'port' => '3306',
        ],
    ],
    'version_order' => 'creation',
];
