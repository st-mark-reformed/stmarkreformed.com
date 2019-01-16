<?php
declare(strict_types=1);

/**
 * CraftCMS front controller
 */

define('CRAFT_BASE_PATH', __DIR__);
define('CRAFT_VENDOR_PATH', CRAFT_BASE_PATH . '/vendor');

require_once CRAFT_VENDOR_PATH . '/autoload.php';

if (file_exists(CRAFT_BASE_PATH . '/.env')) {
    (new Dotenv\Dotenv(CRAFT_BASE_PATH))->load();
}

if (! file_exists(CRAFT_BASE_PATH . '/config/license.key')) {
    /** @noinspection PhpUnhandledExceptionInspection */
    throw new \Exception(
        'Please place the license.key file at `config/license.key`'
    );
}

define('CRAFT_ENVIRONMENT', getenv('ENVIRONMENT') ?: 'dev');

if (PHP_SAPI === 'cli') {
    $app = require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/console.php';
    $exitCode = $app->run();
    exit($exitCode);
}

$app = require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/web.php';
$app->run();
