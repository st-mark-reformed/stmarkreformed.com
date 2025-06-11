<?php

declare(strict_types=1);

namespace Config\Dependencies;

use App\Persistence\RootPdo;
use App\Persistence\RootPdoFactory;
use PDO;
use Psr\Container\ContainerInterface;
use RuntimeException;
use RxAnte\AppBootstrap\Dependencies\Bindings;

use function assert;

readonly class RegisterBindingsPersistence
{
    public static function register(Bindings $bindings): void
    {
        $bindings->addBinding(
            PDO::class,
            static fn () => throw new RuntimeException(
                'Use a PDO implementation for a specific database',
            ),
        );

        $bindings->addBinding(
            RootPdo::class,
            static function (ContainerInterface $container): RootPdo {
                $factory = $container->get(RootPdoFactory::class);
                assert($factory instanceof RootPdoFactory);

                return $factory->create();
            },
        );
    }
}
