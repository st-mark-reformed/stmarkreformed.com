<?php

declare(strict_types=1);

namespace Config\Dependencies;

use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use RxAnte\AppBootstrap\Dependencies\Bindings;

readonly class RegisterBindingsUuid
{
    public static function register(Bindings $bindings): void
    {
        $bindings->addBinding(
            UuidFactoryInterface::class,
            $bindings->autowire(UuidFactory::class),
        );
    }
}
