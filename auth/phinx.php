<?php

declare(strict_types=1);

$dbName = (string) getenv('SMRC_AUTH_DB_NAME');

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/config/Data/Migrations',
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => $dbName,
        $dbName => [
            'adapter' => 'mysql',
            'host' => (string) getenv('SMRC_AUTH_DB_HOST'),
            'name' => $dbName,
            'user' => (string) getenv('SMRC_AUTH_DB_USER'),
            'pass' =>  (string) getenv('SMRC_AUTH_DB_PASSWORD'),
            'port' => '3306',
        ],
    ],
    'version_order' => 'creation',
];
