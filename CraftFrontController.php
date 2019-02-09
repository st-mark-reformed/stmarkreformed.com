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
    // Register handler to catch errors that come up before Yii registers a handler
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler());
    $whoops->register();

    function d()
    {
        foreach (func_get_args() as $arg) {
            var_dump($arg);
        }
    }

    function dd()
    {
        foreach (func_get_args() as $arg) {
            var_dump($arg);
        }
        die;
    }

    $app = require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/console.php';
    $exitCode = $app->run();
    exit($exitCode);
}

if (getenv('DEV_MODE') === 'true') {
    // Register handler to catch errors that come up before Yii registers a handler
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();

    // Configure ref dumper
    /** @noinspection PhpUnhandledExceptionInspection */
    ref::config('shortcutFunc', ['r', 'rt', 'd', 'dd']);

    function d()
    {
        call_user_func_array('r', func_get_args());
    }

    function dd()
    {
        ob_clean();
        call_user_func_array('r', func_get_args());
        die;
    }
}

$app = require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/web.php';
$app->run();
