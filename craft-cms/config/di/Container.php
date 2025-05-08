<?php

declare(strict_types=1);

namespace Config\di;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

use function dirname;
use function getenv;
use function is_dir;
use function mkdir;

class Container
{
    private static ?ContainerInterface $container = null;

    public static function get(): ContainerInterface
    {
        if (self::$container !== null) {
            return self::$container;
        }

        $containerBuilder = (new ContainerBuilder())
            ->useAnnotations(true)
            ->useAutowiring(true)
            ->ignorePhpDocErrors(true)
            ->addDefinitions(
                require __DIR__ . '/dependencies.php'
            );

        if ((bool) getenv('ENABLE_DI_COMPILATION')) {
            $diCacheDir = dirname(__DIR__, 2) . '/storage/di-cache';

            if (! is_dir($diCacheDir)) {
                mkdir(
                    $diCacheDir,
                    0777,
                    true
                );
            }

            $containerBuilder->enableCompilation($diCacheDir);

            $containerBuilder->writeProxiesToFile(
                true,
                $diCacheDir
            );
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        self::$container = $containerBuilder->build();

        return self::$container;
    }
}
