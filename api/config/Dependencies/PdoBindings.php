<?php

declare(strict_types=1);

namespace Config\Dependencies;

use App\Persistence\ApiPdo;
use App\Persistence\ApiPdoFactory;
use PDO;
use Psr\Container\ContainerInterface;
use RuntimeException;
use RxAnte\AppBootstrap\Dependencies\Bindings;

readonly class PdoBindings
{
    public function __invoke(Bindings $bindings): void
    {
        $bindings->addBinding(
            PDO::class,
            static fn () => throw new RuntimeException(
                'Use a PDO implementation for a specific database',
            ),
        );

        $bindings->addBinding(
            ApiPdo::class,
            static function (ContainerInterface $di): ApiPdo {
                return $di->get(ApiPdoFactory::class)->create();
            },
        );
    }
}
