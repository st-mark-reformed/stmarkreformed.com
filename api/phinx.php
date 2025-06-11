<?php

declare(strict_types=1);

use Config\RuntimeConfig;
use Config\RuntimeConfigOptions;

// phpcs:disable SlevomatCodingStandard.Functions.StaticClosure.ClosureNotStatic

return [
    'templates' => [
        'file' => '%%PHINX_CONFIG_DIR%%/src/Persistence/Migrate/Migration.change.template.php.dist',
        'seedFile' => '%%PHINX_CONFIG_DIR%%/src/Persistence/Seed/Seed.template.php.dist',
    ],
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/config/Data/Migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/config/Data/Seeds',
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_environment' => 'smrc_api',
        'smrc_api' => (function () {
            $runtimeConfig = new RuntimeConfig();

            return [
                'adapter' => 'mysql',
                'host' => $runtimeConfig->getString(
                    RuntimeConfigOptions::API_DB_HOST,
                ),
                'name' => $runtimeConfig->getString(
                    RuntimeConfigOptions::API_DB_NAME,
                ),
                'user' => $runtimeConfig->getString(
                    RuntimeConfigOptions::API_DB_USER,
                ),
                'pass' =>  $runtimeConfig->getString(
                    RuntimeConfigOptions::API_DB_PASSWORD,
                ),
                'port' => $runtimeConfig->getString(
                    RuntimeConfigOptions::API_DB_PORT,
                ),
            ];
        })(),
    ],
    'version_order' => 'creation',
];
